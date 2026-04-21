<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $payload = $notification->toSms($notifiable);

        if (is_string($payload)) {
            $payload = ['message' => $payload];
        }

        if (! is_array($payload) || empty($payload['message'])) {
            return;
        }

        Log::info('sms.notification', [
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id ?? null,
            'message' => (string) $payload['message'],
            'module' => $payload['module'] ?? null,
            'resource_id' => $payload['resource_id'] ?? null,
        ]);
    }
}
