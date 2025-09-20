<?php

// app/Notifications/VendorRegistered.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification; // optional if you also want DB via Filament

class VendorRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $vendorUrl
    ) {}

    public function via($notifiable): array
    {
        // Send both email AND store a database copy (Laravel DB channel)
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Vendor Registered')
            ->greeting('Hello!')
            ->line("{$this->firstName} {$this->lastName} just registered.")
            ->action('View Vendor', $this->vendorUrl)
            ->line('Thanks!');
    }

    // Optional: store a generic payload in notifications table
    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'New Vendor Registered',
            'message' => "{$this->firstName} {$this->lastName} just registered.",
            'url' => $this->vendorUrl,
        ];
    }
}
