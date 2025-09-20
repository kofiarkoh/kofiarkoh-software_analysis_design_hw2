<?php

namespace App\Jobs;

use App\Utils\Arkesel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendVendorOrderConfirmationSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $phoneNumber,
        public string $message
    ) {
        // tag the job (helps with Horizon/observability)
        $this->onQueue('notifications'); // optional named queue
    }

    // Optional: retry/backoff
    public int $tries = 3;
    public int $backoff = 10; // seconds between retries

    public function handle(): void
    {
        $arkesel = new Arkesel();
        $arkesel->sendSMS($this->message, [$this->phoneNumber]);
    }
}
