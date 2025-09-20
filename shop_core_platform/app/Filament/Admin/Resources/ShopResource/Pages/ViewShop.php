<?php

namespace App\Filament\Admin\Resources\ShopResource\Pages;

use App\Filament\Admin\Resources\ShopResource;
use App\Filament\Admin\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShop extends ViewRecord
{
    protected static string $resource = ShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make("edit")->label("Edit Shop Info"),
            Actions\Action::make('view_vendor')->label("View Vendor Profile")->url( fn ($record) =>  VendorResource::getUrl('view', ['record' => $record->owner->id])),
            Actions\Action::make('view_shop_page')->label("View Shop Website")->url( fn ($record) => route('shops.index', ['shop' => $record->slug]))->openUrlInNewTab(true),
        ];
    }
}
