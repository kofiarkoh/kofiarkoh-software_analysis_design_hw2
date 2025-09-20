<?php

namespace App\Filament\Admin\Pages;

use App\Settings\AppSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageApp extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AppSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('order_commission')
                    ->label('Order Commission (%)')
                   // ->numeric()
                    ->required()
                   // ->rules(['numeric', 'min:0.01']),
            ]);
    }
}
