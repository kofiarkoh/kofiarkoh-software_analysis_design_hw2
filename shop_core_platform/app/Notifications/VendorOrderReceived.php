<?php

namespace App\Notifications;

use App\Models\OrderItem;
use App\Models\Shop;
use App\Models\Vendor\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorOrderReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected Collection $orderItems;
    protected Shop $shop;
    /**
     * Create a new notification instance.
     */
    public function __construct(Shop $shop, Collection $orderItems)
    {
        $this->orderItems = $orderItems;
        $this->shop = $shop;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
            ->subject('Order Received')
            ->markdown('emails.vendor_order_received', [
                'notifiable' => $notifiable,
                'orderItems' => $this->orderItems,
                'shop'       => $this->shop,
                'ordersUrl'  => url("/vendor/{$this->shop->id}/order-items"),
            ]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
