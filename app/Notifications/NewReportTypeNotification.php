<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ReportType;

class NewReportTypeNotification extends Notification
{
    use Queueable;

    protected $reportType;

    /**
     * Create a new notification instance.
     */
    public function __construct(ReportType $reportType)
    {
        $this->reportType = $reportType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('A new report type has been created.')
                    ->action('View Report Types', url('/barangay/dashboard'))
                    ->line('Please check your dashboard for more details.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_report_type',
            'title' => 'New Report Type Available',
            'message' => "A new report type '{$this->reportType->name}' has been created. Deadline: " . $this->reportType->deadline->format('M d, Y'),
            'report_type_id' => $this->reportType->id,
            'report_type_name' => $this->reportType->name,
            'frequency' => $this->reportType->frequency,
            'deadline' => $this->reportType->deadline->format('Y-m-d H:i:s'),
            'redirect_url' => route('barangay.dashboard'),
            'created_at' => now()->toDateTimeString()
        ];
    }
}
