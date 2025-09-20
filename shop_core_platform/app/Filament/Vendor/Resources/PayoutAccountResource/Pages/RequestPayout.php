<?php

namespace App\Filament\Vendor\Resources\PayoutAccountResource\Pages;

use App\Filament\Vendor\Resources\PayoutAccountResource;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\Vendor\ShopPayment;
use App\States\Transaction\FailedTransaction;
use App\States\Transaction\PendingTransaction;
use App\Utils\Payment\PayStack;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use App\Models\Vendor\PayoutAccount;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RequestPayout extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PayoutAccountResource::class;

    protected static string $view = 'filament.vendor.resources.payout-account-resource.pages.request-payout';

    public array $formData = [];


    public function mount(): void
    {
        $this->form->fill();
    }



    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->numeric()
                ->required()
                ->minValue(1),

            Forms\Components\Select::make('payout_account_id')
                ->label('Payout Account')
                ->options(
                    PayoutAccount::query()
                        ->where('shop_id', Filament::getTenant()->id ?? null)
                        ->pluck('account_name', 'id')
                )
                ->required()
                ->searchable(),
        ];
    }


    protected function getFormStatePath(): string
    {
        return 'formData';
    }

    /**
     * @throws ValidationException
     */
    public function submit(): void
    {
        $data = $this->form->getState();

        /** @var Shop $shop */
        $shop = Filament::getTenant();


        $shopBalance  = $shop->balance();
        if ($data['amount'] > $shopBalance) {
            throw ValidationException::withMessages([
                'formData.amount' => 'The requested amount exceeds your balance.',
            ]);
        }

        $balance = $shopBalance - $data['amount'];



        $payoutAccount = PayoutAccount::where('id', $data['payout_account_id'])->first();

        /** @var ShopPayment $payment */
        $payment = $shop->payments()->create([
            'amount' => - $data['amount'],
            'balance' => $balance ,
            'payment_type' =>  ShopPayment::DEBIT_PAYMENT,
            'reference' => Transaction::CATEGORY_VENDOR_PAYOUT,
        ]);

        $transactionReference  = (string) Str::uuid();

        $transaction  = $payment->transaction()->create([
            'order_id' => null,
            'user_id' => null,
            'category' => Transaction::CATEGORY_VENDOR_PAYOUT,
            'status' => (string) PendingTransaction::class,
            'amount' => $data['amount'],
            'uuid' => $transactionReference ,
            'payment_url' => null,
        ]);

        $transferResponse = PayStack::makeSingleTransfer([
            'amount' => $data['amount'],
            'reference' => $transactionReference,
            'recipient' => $payoutAccount->paystack_recipient_code,
            'reason' => Transaction::CATEGORY_VENDOR_PAYOUT,
        ]);

        $transaction->paystack_initial_response = $transferResponse;


        if (!($transferResponse['status'] ?? false)) {
            // transfer failed instantly on paystack. reverse amount and mark transaction as failed
            $transaction->status->transitionTo(FailedTransaction::class);

            $shop->reversePayoutTransfer($transaction, $payment);
        }
        $transaction->save();



        Notification::make()
            ->title('Payout Request Submitted')
            ->success()
            ->body("₵{$data['amount']} will be sent to the selected account")
            ->send();

        $this->form->fill(); // Optionally clear the form
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }

}
