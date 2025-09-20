<?php

namespace App\Filament\Vendor\Pages;

use App\Models\Admin\Admin;
use App\Models\Vendor\Vendor;
use App\Notifications\VendorRegistered;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register;

class RegisterVendor extends Register
{
    // protected static string $view = 'filament.vendor.pages.register-vendor';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected string $userModel = Vendor::class;


    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['status'] = Vendor::STATUS_UNDER_REVIEW;
        return $data;
    }

    public function register():  ?RegistrationResponse
    {
        parent::register();
       $adminUsers = Admin::all();

       $vendor = Filament::auth('vendor')->user();
        if ($vendor) {

            $url = route('filament.admin.resources.vendors.view', $vendor->id);

            Notification::make()
                ->title('New Vendor Registered')
                ->body("{$vendor->first_name} {$vendor->last_name} has just registered.")
                ->actions([
                    Action::make('View Vendor')
                        ->url($url)

                ])
                ->sendToDatabase($adminUsers);

            \Illuminate\Support\Facades\Notification::send(
                $adminUsers,
                new VendorRegistered($vendor->first_name, $vendor->last_name, $url)
            );
        }

        return app(RegistrationResponse::class);

    }
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('first_name')
                            ->required(),

                        TextInput::make('last_name')
                            ->required(),
                        TextInput::make('phone_number')
                            ->required() ->rule([
                               // 'regex:/^0(24|25|26|27|28|50|54|55|56|57|58|59)[0-9]{7}$/',
                                'min:10',
                                'max:10',
                            ])
                            ->validationMessages([
                                '*' => 'Please enter a valid 10-digit phone number (e.g., 0244123456).'
                            ]),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }


}
