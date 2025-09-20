<?php

namespace App\Filament\Vendor\Pages;

use App\Models\Shop;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterShop extends RegisterTenant
{
    //protected static ?string $navigationIcon = 'heroicon-o-document-text';

    //protected static string $view = 'filament.pages.register-school';

    protected static ?string $slug = 'shops/create';

    public static function getLabel(): string
    {
        return 'Register Shop';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
            ]);
    }

    protected function handleRegistration(array $data): Shop
    {
        $user = auth()->user();
        $team = Shop::create($data +
            [
            'owner_id' => $user->id,
                'status' => Shop::STATUS_UNDER_REVIEW,
                ]);

        $team->users()->attach(auth()->user());

        return $team;
    }


}
