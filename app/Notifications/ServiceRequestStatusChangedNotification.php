<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly ServiceRequest $serviceRequest,
        public readonly string $oldStatus,
        public readonly string $newStatus
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'in_progress' => 'Your service request is now being worked on.',
            'completed' => 'Great news! Your service request has been completed.',
            'cancelled' => 'Your service request has been cancelled.',
            'on_hold' => 'Your service request has been placed on hold.',
        ];

        $message = $statusMessages[$this->newStatus] ?? "Your service request status has changed to {$this->newStatus}.";

        return (new MailMessage)
            ->subject('Service Request Update - #'.$this->serviceRequest->id)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($message)
            ->line('**Previous Status:** '.ucfirst(str_replace('_', ' ', $this->oldStatus)))
            ->line('**New Status:** '.ucfirst(str_replace('_', ' ', $this->newStatus)))
            ->action('View Request', url('/service-requests/'.$this->serviceRequest->id))
            ->line('Thank you for using our facilities management system.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $severityMap = [
            'completed' => 'success',
            'cancelled' => 'error',
            'on_hold' => 'warning',
            'in_progress' => 'info',
        ];

        return [
            'type' => 'service_request_status_changed',
            'title' => 'Service Request Updated',
            'message' => "Service request #{$this->serviceRequest->id} status changed from ".ucfirst(str_replace('_', ' ', $this->oldStatus)).' to '.ucfirst(str_replace('_', ' ', $this->newStatus)).'.',
            'service_request_id' => $this->serviceRequest->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action_url' => '/service-requests/'.$this->serviceRequest->id,
            'icon' => 'bell',
            'severity' => $severityMap[$this->newStatus] ?? 'info',
        ];
    }
}
