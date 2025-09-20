<?php

namespace App\Filament\Vendor\Pages;

use App\Models\Vendor\Vendor;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class EditAccount extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.vendor.pages.edit-account';
    protected static ?string $title = 'My Account';


    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $phone_number = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'first_name' => Auth::user()->first_name,
            'last_name' => Auth::user()->last_name,
            'phone_number' => Auth::user()->phone_number,
            'email' => Auth::user()->email,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('first_name')->required()->disabled(),
            Forms\Components\TextInput::make('last_name')->required()->disabled(),
            TextInput::make('phone_number')
                ->required() ->rule([
                    //'regex:/^0(24|25|26|27|28|50|54|55|56|57|58|59)[0-9]{7}$/',
                    'min:10',
                    'max:10',
                ])
                ->validationMessages([
                    '*' => 'Please enter a valid 10-digit phone number (e.g., 0244123456).'
                ])->disabled(),
            Forms\Components\TextInput::make('email')->required()->email()->disabled(),
        ];
    }

    protected function getFormModel(): Model|string|null
    {
        return Auth::user();
    }

    public function submit(): void
    {
        /** @var Vendor $user */
        $user = Auth::user();
        $data = $this->form->getState();

        $user->update($data);

        Notification::make()->title('Account updated successfully.')->success()->send();
    }


    public function getHeaderActions(): array
    {
        return  [
            Action::make('verify_phone_number')
                ->label('Verify Phone Number')
                ->visible(function () {
                    $vendor = Filament::auth()->user();
                    return !$vendor->phone_number_verified_at;
                })
                ->beforeFormFilled(function (array $data): void {

                    /** @var Vendor $vendor */
                    $vendor = Filament::auth()->user();
                    $vendor->sendOTPToken();
                })
                ->form([
                    TextInput::make('token')
                        ->label('Enter OTP Token sent')
                        ->required(),
                ])
                ->action(function (array $data): void {

                    /** @var Vendor $vendor */
                    $vendor = Filament::auth()->user();

                    $latestToken = $vendor->getLatestUnexpiredOTPToken();

                    // Handle the submitted data here
                    $userToken = $data['token'];

                    if (!$latestToken) {
                        Notification::make()
                            ->title("Invalid OTP Token.")
                            ->success()
                            ->send();
                        return;
                    }
                    if ($latestToken == $userToken) {
                        $vendor->phone_number_verified_at = now();
                        $vendor->save();
                        Notification::make()
                            ->title("You have successfully verified your phone number.")
                            ->success()
                            ->send();
                    }
                    else{
                        Notification::make()
                            ->title("Invalid OTP Token.")
                            ->danger()
                            ->send();
                    }

                }),
        ];
    }
}
