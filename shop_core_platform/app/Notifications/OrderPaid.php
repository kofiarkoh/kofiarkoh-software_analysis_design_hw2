<?php

namespace App\Notifications;

use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaid extends Notification implements  ShouldQueue
{
    use Queueable;

    protected Collection $orderItems;
    /**
     * Create a new notification instance.
     * @param Collection $orderItems
     */
    public function __construct(Collection $orderItems)
    {
        $this->orderItems = $orderItems;
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
//        $message = (new MailMessage)
//            ->subject('Order Summary')
//            ->greeting('Hello ' . $notifiable->first_name . ',')
//            ->line('Thank you for placing an order. Here are the details:');
//
//        foreach ($this->orderItems as $item) {
//            $product = $item->product;
//
//            $message->line(
//                sprintf(
//                    '- %s (Qty: %d) — GH₵ %s',
//                    $product->name,
//                    $item->quantity,
//                    number_format($item->price, 2)
//                )
//            );
//        }
//
//        return $message
//            // ->action('View Order', url("/orders/{$this->order->id}"))
//            ->line('We appreciate your business!');

        return (new MailMessage)
            ->subject('Order Summary')
            ->markdown('emails.user.order_paid', [
                'notifiable' => $notifiable,
                'orderItems' => $this->orderItems,
                //'shop'       => $this->shop,
                'ordersUrl'  => url("/orders/{$this->orderItems->first()->order->id}")
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
