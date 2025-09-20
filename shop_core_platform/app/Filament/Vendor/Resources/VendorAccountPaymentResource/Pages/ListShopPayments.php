<?php

namespace App\Filament\Vendor\Resources\VendorAccountPaymentResource\Pages;

use App\Filament\Vendor\Resources\ShopPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopPayments extends ListRecords
{
    protected static string $resource = ShopPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
