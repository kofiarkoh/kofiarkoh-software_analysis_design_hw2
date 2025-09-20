<?php

namespace App\Filament\Admin\Resources\VendorResource\Pages;

use App\Filament\Admin\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
