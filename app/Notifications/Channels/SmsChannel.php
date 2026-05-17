<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $notification->toSms($notifiable);
    }
}
