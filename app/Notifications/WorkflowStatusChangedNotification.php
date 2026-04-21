<?php

namespace App\Notifications;

use App\Notifications\Channels\PushChannel;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class WorkflowStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $module,
        private readonly int $resourceId,
        private readonly ?string $fromStatus,
        private readonly string $toStatus,
        private readonly ?string $url,
        private readonly ?string $actor,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [
            'database',
            SmsChannel::class,
            PushChannel::class,
        ];

        if (! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title(),
            'text' => $this->message(),
            'module' => $this->module,
            'resource_id' => $this->resourceId,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
            'url' => $this->url,
            'actor' => $this->actor,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title())
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line($this->message());

        if ($this->url) {
            $mail->action(__('Open Record'), $this->url);
        }

        return $mail;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSms(object $notifiable): array
    {
        return [
            'message' => $this->message(),
            'module' => $this->module,
            'resource_id' => $this->resourceId,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toPush(object $notifiable): array
    {
        return [
            'title' => $this->title(),
            'body' => $this->message(),
            'module' => $this->module,
            'resource_id' => $this->resourceId,
            'url' => $this->url,
        ];
    }

    private function title(): string
    {
        return sprintf(
            '%s status updated',
            Str::headline($this->module),
        );
    }

    private function message(): string
    {
        $from = $this->fromStatus ? __(' from :from', ['from' => $this->fromStatus]) : '';
        $actor = $this->actor ? __(' by :actor', ['actor' => $this->actor]) : '';

        return __('Status changed: :module #:id:from to :to:actor', [
            'module' => Str::headline($this->module),
            'id' => $this->resourceId,
            'from' => $from,
            'to' => $this->toStatus,
            'actor' => $actor,
        ]);
    }
}
