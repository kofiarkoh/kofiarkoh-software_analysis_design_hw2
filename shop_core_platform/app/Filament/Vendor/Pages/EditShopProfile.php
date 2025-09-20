<?php

namespace App\Filament\Vendor\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Forms\Form;
use Webbingbrasil\FilamentCopyActions\Pages\Actions\CopyAction;

class EditShopProfile extends EditTenantProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.vendor.pages.edit-shop-profile';

    public static function getLabel(): string
    {
        return 'Shop Information';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_shop_page')->label("View Shop Website")->url( fn ($record) => route('shops.index', ['shop' =>  filament()->getTenant()->slug]))->openUrlInNewTab(true),
            CopyAction::make()->label("Copy Shop Website Link")->copyable(fn ($record) => url('/shops/' . filament()->getTenant()->slug)),
        ];
    }
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name'),
            TextInput::make('status')->disabled(),
            TextInput::make('shop_url')
                ->label('Shop URL')
                ->disabled()
                ->formatStateUsing(fn ($record) => $record?->slug
                    ? url('/shops/' . $record->slug)
                    : '—'),

        ]);
    }
}
