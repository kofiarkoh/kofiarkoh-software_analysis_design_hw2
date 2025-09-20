<?php

namespace App\Observers;

use App\Models\Shop;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\ShopStatusChangedMail;

class ShopObserver
{
    public function updated(Shop $shop): void
    {
        // only act when 'status' actually changed
        if (! $shop->wasChanged('status')) {
            return;
        }

        $old = $shop->getOriginal('status');
        $new = $shop->status;

        $owner = $shop->owner;
        if (! $owner) return;

        $url = route('filament.vendor.pages.dashboard', ['tenant' => $shop]);

        Notification::make()
            ->title('Shop status updated')
            ->body("**{$shop->name}** changed from {$old} to {$new}.")
            ->icon('heroicon-o-arrow-path')
            ->sendToDatabase($owner);

        NotificationFacade::send($owner, new ShopStatusChangedMail($shop, $old, $new, $url));
    }
}
