<?php

namespace App\Filament\Admin\Resources\DeliveryRegionResource\Pages;

use App\Filament\Admin\Resources\DeliveryRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeliveryRegion extends ViewRecord
{
    protected static string $resource = DeliveryRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
