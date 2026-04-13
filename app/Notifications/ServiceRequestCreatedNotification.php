<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly ServiceRequest $serviceRequest
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
        $category = $this->serviceRequest->category;

        return (new MailMessage)
            ->subject('Service Request Created - #'.$this->serviceRequest->id)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A new service request has been created.')
            ->line('**Category:** '.($category?->name ?? 'General'))
            ->line('**Priority:** '.ucfirst($this->serviceRequest->priority))
            ->line('**Description:** '.$this->serviceRequest->description)
            ->action('View Request', url('/service-requests/'.$this->serviceRequest->id))
            ->line('We will address your request as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $category = $this->serviceRequest->category;

        return [
            'type' => 'service_request_created',
            'title' => 'Service Request Created',
            'message' => "Your service request #{$this->serviceRequest->id} has been submitted successfully.",
            'service_request_id' => $this->serviceRequest->id,
            'category' => $category?->name ?? 'General',
            'priority' => $this->serviceRequest->priority,
            'status' => $this->serviceRequest->status,
            'description' => $this->serviceRequest->description,
            'action_url' => '/service-requests/'.$this->serviceRequest->id,
            'icon' => 'wrench',
            'severity' => 'info',
        ];
    }
}
