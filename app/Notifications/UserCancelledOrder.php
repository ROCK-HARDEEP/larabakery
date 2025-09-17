<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserCancelledOrder extends Notification
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
            'type' => 'user_cancelled_order',
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'message' => 'User cancelled order #'.$this->order->id,
        ];
    }
}


