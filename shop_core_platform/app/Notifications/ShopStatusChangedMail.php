<?php

// app/Notifications/ShopStatusChangedMail.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Shop;

class ShopStatusChangedMail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shop $shop,
        public string $old,
        public string $new,
        public string $url
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your shop status changed')
            ->greeting('Hello!')
            ->line("Your shop, {$this->shop->name} changed from {$this->old} to {$this->new}.")
            ->action('View Shop', $this->url)
            ->line('If you didn’t expect this change, please contact support.');
    }
}
