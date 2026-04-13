<?php

namespace App\Notifications;

use App\Models\Lease;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaseExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Lease $lease
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
        $daysUntilExpiry = now()->diffInDays($this->lease->end_date);
        $unit = $this->lease->unit;

        return (new MailMessage)
            ->subject('Lease Expiring Soon - '.$unit->unit_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Your lease for unit {$unit->unit_number} is expiring in {$daysUntilExpiry} days.")
            ->line('Lease End Date: '.$this->lease->end_date->format('F j, Y'))
            ->action('View Lease Details', url('/leases/'.$this->lease->id))
            ->line('Please contact management if you wish to renew your lease.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'lease_expiring',
            'title' => 'Lease Expiring Soon',
            'message' => "Your lease for unit {$this->lease->unit->unit_number} is expiring on {$this->lease->end_date->format('F j, Y')}.",
            'lease_id' => $this->lease->id,
            'unit_id' => $this->lease->unit_id,
            'unit_number' => $this->lease->unit->unit_number,
            'end_date' => $this->lease->end_date->toDateString(),
            'days_until_expiry' => now()->diffInDays($this->lease->end_date),
            'action_url' => '/leases/'.$this->lease->id,
            'icon' => 'calendar-clock',
            'severity' => 'warning',
        ];
    }
}
