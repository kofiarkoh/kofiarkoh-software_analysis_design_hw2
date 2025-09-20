<?php

namespace App\Filament\Vendor\Widgets;

use App\Models\Vendor\Vendor;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class AccountStatusWidget extends BaseWidget
{
    protected static string $view = 'filament.vendor.widgets.account-status-widget';

    // Change from 'full' to a fixed span, like 1 or 2
    protected int | string | array $columnSpan = 1; // or 2, 3, etc.


    public function getViewData(): array
    {
        $vendor = Filament::auth()->user(); // Adjust this if needed

        return [
            'status' => $vendor->status ?? 'Unknown',
            'description' => Vendor::VENDOR_STATUS_DESCRIPTIONS[$vendor->status] ?? 'No description available.',
        ];
    }

}
