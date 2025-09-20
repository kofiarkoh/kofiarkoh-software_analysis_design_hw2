<?php

namespace App\Utils;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Arkesel
{

    protected  $baseUrl = '';
    protected  $apiKey = '';
    protected $senderId = '';

    public function __construct()
    {
        $this->baseUrl = env('ARKESEL_API_URL');
        $this->apiKey = env('ARKESEL_API_KEY');
        $this->senderId = env('SMS_SENDER_ID');
    }


    public function sendSMS(string $message, array $recipients): Response
    {

        $recipients = array_map(function ($number) {
            // Remove '+' if present
            $number = ltrim($number, '+');

            // Replace only the first leading 0 with 233
            return preg_replace('/^0/', '233', $number);
        }, $recipients);


        $response = Http::withHeaders(
            [
                "api-key" => $this->apiKey,
            ]
        )->post(
            url: $this->baseUrl . '/v2/sms/send',
            data: [
                "action" => 'send-sms',
                "sender" => $this->senderId,
                "message" => $message,
                "recipients" => $recipients,
                "sandbox" => env('ARKESEL_SANDBOX_MODE')
            ]
        );

        Log::channel('sms')->info($response->status());
        Log::channel('sms')->info($response->json());

        return $response;
    }
}
