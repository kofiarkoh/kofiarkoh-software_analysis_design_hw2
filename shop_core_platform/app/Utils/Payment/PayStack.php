<?php

namespace App\Utils\Payment;

use Illuminate\Support\Facades\Http;

class PayStack
{
    public const TRANSFER_SUCCESS = 'transfer.success';
    public const TRANSFER_FAILED = 'transfer.failed';
    public const TRANSFER_REVERSED = 'transfer.reversed';
    public const TRANSFER_EVENTS = [self::TRANSFER_SUCCESS, self::TRANSFER_FAILED, self::TRANSFER_REVERSED];
    static function generatePaymentRequestUrl($data)
    {
        $apiBaseUrl = env('PAYSTACK_API_BASE_URL');
        $apiKey = env('PAYSTACK_API_KEY');

        return Http::withHeaders(
            [
                'Authorization' => "Bearer $apiKey",

                'content-type' => 'application/json'
            ]
        )->post(
            "$apiBaseUrl/transaction/initialize",
            [
                'amount' => $data['amount'] * 100, //converts amount to pesewas
                'email' => "kofiarkoh0@gmail.com",
                'currency' => 'GHS',
                'callback_url' => '',
                'reference' => $data['transactionId'],

            ]
        )->json();
    }

    static function addTransferRecipient(array $data)
    {
        $apiBaseUrl = env('PAYSTACK_API_BASE_URL');
        $apiKey = env('PAYSTACK_API_KEY');

        return Http::withHeaders(
            [
                'Authorization' => "Bearer $apiKey",

                'content-type' => 'application/json'
            ]
        )->post(
            "$apiBaseUrl/transferrecipient",
            [
                'type' => $data['account_type'],
                'name' => $data['account_name'],
                'account_number' => $data['account_number'],
                'bank_code' => $data['bank_code'],
                'currency' => "GHS"
            ]
        )->json();
    }

    static function makeSingleTransfer(array $data)
    {
        $apiBaseUrl = env('PAYSTACK_API_BASE_URL');
        $apiKey = env('PAYSTACK_API_KEY');

        return Http::withHeaders(
            [
                'Authorization' => "Bearer $apiKey",

                'content-type' => 'application/json'
            ]
        )->post(
            "$apiBaseUrl/transfer",
            [
                'source' => 'balance',
                'amount' => $data['amount'],
                'reference' => $data['reference'],
                'recipient' => $data['recipient'],
                'reason' => $data['reason'],
            ]
        )->json();
    }
}

