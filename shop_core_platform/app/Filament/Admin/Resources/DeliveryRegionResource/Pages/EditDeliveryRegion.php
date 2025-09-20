<?php

namespace App\Filament\Admin\Resources\DeliveryRegionResource\Pages;

use App\Filament\Admin\Resources\DeliveryRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryRegion extends EditRecord
{
    protected static string $resource = DeliveryRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
