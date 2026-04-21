<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toPush')) {
            return;
        }

        $payload = $notification->toPush($notifiable);

        if (! is_array($payload) || empty($payload['title']) || empty($payload['body'])) {
            return;
        }

        Log::info('push.notification', [
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id ?? null,
            'title' => (string) $payload['title'],
            'body' => (string) $payload['body'],
            'module' => $payload['module'] ?? null,
            'resource_id' => $payload['resource_id'] ?? null,
        ]);
    }
}
