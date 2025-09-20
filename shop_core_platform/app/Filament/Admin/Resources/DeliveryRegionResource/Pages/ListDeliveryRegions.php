<?php

namespace App\Filament\Admin\Resources\DeliveryRegionResource\Pages;

use App\Filament\Admin\Resources\DeliveryRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryRegions extends ListRecords
{
    protected static string $resource = DeliveryRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
