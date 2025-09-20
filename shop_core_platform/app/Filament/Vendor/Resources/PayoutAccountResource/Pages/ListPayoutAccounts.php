<?php

namespace App\Filament\Vendor\Resources\PayoutAccountResource\Pages;

use App\Filament\Vendor\Resources\PayoutAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayoutAccounts extends ListRecords
{
    protected static string $resource = PayoutAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('request_payout')
                ->label('Request Payout')
                ->url(RequestPayout::getUrl()) // CustomForm is your custom page class
                ->color('primary'),
        ];
    }
}
