<?php

namespace App\Filament\Vendor\Resources\PayoutAccountResource\Pages;

use App\Filament\Vendor\Resources\PayoutAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayoutAccount extends EditRecord
{
    protected static string $resource = PayoutAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}
