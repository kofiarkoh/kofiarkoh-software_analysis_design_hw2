<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendMockPaystackWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;                    // retry up to 5 times
    public int $backoff = 5;                  // seconds between retries (exponential by default in Laravel 11)
    public $timeout = 10;                     // job times out after 10 seconds

    public function __construct(
        public string $reference,
        public int $amount,
        public string $currency = 'GHS',
        public bool $isFollowUp = false
    ) {}

    public function handle(): void
    {
        $webhookUrl = config('services.paystack.webhook_url');
        $secret     = config('services.paystack.webhook_secret') ?? '';

        if (!$webhookUrl) {
            // No destination configured; just exit quietly.
            return;
        }

        // 🎲 Outcome selection:
        //  - 20% pending first (then schedule a final)
        //  - otherwise: 70% success / 30% failed directly
        $roll = random_int(1, 100);
        if (!$this->isFollowUp && $roll <= 20) {
            $this->sendWebhook($webhookUrl, $secret, 'pending');
            // Queue a final outcome shortly after
            dispatch(new self($this->reference, $this->amount, $this->currency, true))
                ->delay(now()->addSeconds(random_int(3, 10)));
            return;
        }

        $finalStatus = ($roll <= 85) ? 'success' : 'failed';
        $this->sendWebhook($webhookUrl, $secret, $finalStatus);
    }

    private function sendWebhook(string $url, string $secret, string $status): void
    {
        $payload = [
            'event' => "charge.$status",     // your controller uses status inside data
            'data'  => [
                'reference' => $this->reference,
                'status'    => $status,       // 'success' | 'pending' | 'failed'
                'amount'    => $this->amount,
                'currency'  => $this->currency,
                'paid_at'   => now()->toIso8601String(),
            ],
        ];

        $raw       = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha512', $raw, $secret);

        Http::timeout(5)
            ->withHeaders([
                'X-Paystack-Signature' => $signature,
                'Content-Type'         => 'application/json',
            ])
            ->post($url, $payload)
            ->throw(); // fail the job (and retry) on non-2xx
    }
}
