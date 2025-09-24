<?php

// app/Http/Controllers/PaystackMockController.php
namespace App\Http\Controllers;

use App\Jobs\SendMockPaystackWebhook;
use App\Models\PaymentTransaction;
use App\Models\TransferRecipient;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackMockController extends Controller
{

    public function initializeTransaction(Request $request)
    {
        $validated = $request->validate([
            'amount'       => ['required', 'integer', 'min:100'],
            'email'        => ['required', 'email'],
            'currency'     => ['nullable', 'string', 'max:10'],
            'callback_url' => ['nullable', 'url'],
            'reference'    => ['required', 'string'],
        ]);

        $accessCode = 'AC_' . Str::upper(Str::random(16));
        $authUrl = config('app.url') . '/mock/checkout/' . $accessCode;

        $txn = PaymentTransaction::create([
            'reference'         => $validated['reference'],
            'amount'            => $validated['amount'],
            'currency'          => $validated['currency'] ?? 'GHS',
            'email'             => $validated['email'],
            'status'            => 'initialized',
            'access_code'       => $accessCode,
            'authorization_url' => $authUrl,
        ]);

        // Kick off async webhook simulation
        SendMockPaystackWebhook::dispatch(
            reference: $txn->reference,
            amount: $txn->amount,
            currency: $txn->currency
        )->delay(now()->addSeconds(random_int(2, 6)));

        return response()->json([
            'status'  => true,
            'message' => 'Authorization URL created',
            'data'    => [
                'authorization_url' => $txn->authorization_url,
                'access_code'       => $txn->access_code,
                'reference'         => $txn->reference,
            ],
        ], 201);
    }


    /**
     * POST /transferrecipient
     * Expects: type, name, account_number, bank_code, currency
     * Returns Paystack-like recipient object.
     */
    public function addTransferRecipient(Request $request)
    {
        $validated = $request->validate([
            'type'           => ['required', 'string'],
            'name'           => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'bank_code'      => ['required', 'string'],
            'currency'       => ['nullable', 'string', 'max:10'],
        ]);

        Log::info('Creating transfer recipient', $validated);
        $recipientCode = 'RCP_' . Str::upper(Str::random(10));

        $recipient = TransferRecipient::create([
            'recipient_code' => $recipientCode,
            'type'           => $validated['type'],
            'name'           => $validated['name'],
            'account_number' => $validated['account_number'],
            'bank_code'      => $validated['bank_code'],
            'currency'       => $validated['currency'] ?? 'GHS',
            'active'         => true,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Transfer recipient created',
            'data'    => [
                'active'         => $recipient->active,
                'recipient_code' => $recipient->recipient_code,
                'name'           => $recipient->name,
                'type'           => $recipient->type,
                'details'        => [
                    'account_number' => $recipient->account_number,
                    'bank_code'      => $recipient->bank_code,
                ],
                'currency'       => $recipient->currency,
            ],
        ]);
    }

    /**
     * POST /transfer
     * Expects: source, amount, reference, recipient (recipient_code), reason
     * Returns Paystack-like transfer object.
     *
     * NOTE: This mock immediately marks transfers as "success" for happy-path simulation.
     * You can swap to "pending" and provide a separate endpoint to resolve status.
     */
    public function makeTransfer(Request $request)
    {
        $validated = $request->validate([
            'source'    => ['required', 'string'],
            'amount'    => ['required', 'integer', 'min:100'],
            'reference' => ['required', 'string'],
            'recipient' => ['required', 'string'], // recipient_code
            'reason'    => ['nullable', 'string'],
        ]);

        // Optionally verify recipient exists
        $recipient = TransferRecipient::where('recipient_code', $validated['recipient'])->first();
        if (!$recipient) {
            return response()->json([
                'status'  => false,
                'message' => 'Recipient not found',
                'data'    => null,
            ], 422);
        }

        $transferCode = 'TRF_' . Str::upper(Str::random(12));

        $transfer = Transfer::create([
            'transfer_code' => $transferCode,
            'amount'        => $validated['amount'],
            'currency'      => 'GHS',
            'reference'     => $validated['reference'],
            'recipient'     => $validated['recipient'],
            'reason'        => $validated['reason'] ?? null,
            'source'        => $validated['source'],
            'status'        => 'success', // or 'pending'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Transfer queued',
            'data'    => [
                'transfer_code' => $transfer->transfer_code,
                'amount'        => $transfer->amount,
                'currency'      => $transfer->currency,
                'reference'     => $transfer->reference,
                'recipient'     => $transfer->recipient,
                'status'        => $transfer->status,
                'reason'        => $transfer->reason,
                'source'        => $transfer->source,
                'created_at'    => $transfer->created_at?->toISOString(),
            ],
        ]);
    }
}

