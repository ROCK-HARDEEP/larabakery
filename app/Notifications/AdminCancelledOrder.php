<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminCancelledOrder extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'admin_cancelled_order',
            'order_id' => $this->order->id,
            'message' => 'Your order #'.$this->order->id.' was cancelled by admin.',
        ];
    }
}


