<?php

namespace App\Filament\Vendor\Resources\VendorAccountPaymentResource\Pages;

use App\Filament\Vendor\Resources\ShopPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViewShopPayment extends ViewRecord
{


    protected static string $resource = ShopPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
