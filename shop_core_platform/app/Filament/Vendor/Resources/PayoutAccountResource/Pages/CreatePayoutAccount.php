<?php

namespace App\Filament\Vendor\Resources\PayoutAccountResource\Pages;

use App\Filament\Vendor\Resources\PayoutAccountResource;
use App\Models\Vendor\PayoutAccount;
use App\Utils\Payment\PayStack;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePayoutAccount extends CreateRecord
{
    protected static string $resource = PayoutAccountResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        $record = $this->getRecord();
        if ($record->trashed()) {
            return null;
        }
        return parent::getCreatedNotification();
    }


    protected function afterCreate(): void
    {
        /** @var PayoutAccount $payoutAccount */
        $payoutAccount = $this->getRecord();

        $paymentResponse = PayStack::addTransferRecipient([
            'account_type' => $payoutAccount->account_type,
            'account_name' => $payoutAccount->account_name,
            'account_number' => $payoutAccount->account_number,
            'bank_code' => $payoutAccount->bank_code,
        ]);

        if (!($paymentResponse['status'] ?? false)) {
            Notification::make()
                ->title('Paystack Registration Failed')
                ->body('Failed to register the payout account with Paystack. Please try again.')
                ->danger()
                ->send();

            $payoutAccount->paystack_response_data = $paymentResponse;
            $payoutAccount->status = PayoutAccount::STATUS_REJECTED_BY_PAYSTACK;
            $payoutAccount->save();
            $payoutAccount->delete();
            return;
        }

        $payoutAccount->paystack_recipient_code = $paymentResponse['data']['recipient_code'];;
        $payoutAccount->paystack_response_data = $paymentResponse;
        $payoutAccount->status = PayoutAccount::STATUS_APPROVED_BY_PAYSTACK;
        $payoutAccount->save();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }


}
