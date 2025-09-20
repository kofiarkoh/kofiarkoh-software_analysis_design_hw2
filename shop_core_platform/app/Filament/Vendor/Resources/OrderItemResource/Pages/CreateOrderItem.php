<?php

namespace App\Filament\Vendor\Resources\OrderItemResource\Pages;

use App\Filament\Vendor\Resources\OrderItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderItem extends CreateRecord
{
    protected static string $resource = OrderItemResource::class;
}
