<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\User;

class ReportRemarksNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $report;
    protected $remarks;
    protected $reportType;
    protected $adminName;

    public function __construct($report, $remarks, $reportType, $adminName)
    {
        $this->report = $report;
        $this->remarks = $remarks;
        $this->reportType = $reportType;
        $this->adminName = $adminName;

        // Log notification creation
        \Illuminate\Support\Facades\Log::info('ReportRemarksNotification created', [
            'report_id' => $this->report->id,
            'report_type' => $this->reportType,
            'report_name' => $this->report->reportType->name,
            'remarks' => $this->remarks,
            'admin_name' => $this->adminName
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Get report details for better context
        $reportDetails = '';
        if ($this->reportType === 'weekly' && isset($this->report->month) && isset($this->report->week_number)) {
            $reportDetails = " ({$this->report->month}, Week {$this->report->week_number})";
        } elseif ($this->reportType === 'monthly' && isset($this->report->month)) {
            $reportDetails = " ({$this->report->month})";
        } elseif ($this->reportType === 'quarterly' && isset($this->report->quarter)) {
            $reportDetails = " (Q{$this->report->quarter})";
        } elseif ($this->reportType === 'semestral' && isset($this->report->semester)) {
            $reportDetails = " ({$this->report->semester} Semester)";
        } elseif ($this->reportType === 'annual' && isset($this->report->year)) {
            $reportDetails = " ({$this->report->year})";
        }

        $fullReportTitle = ($this->report->reportType ? $this->report->reportType->name : 'Report') . $reportDetails;
        $canUpdate = $this->report->can_update ?? false;
        $viewUrl = url('/barangay/submissions');

        // Log email being sent
        \Illuminate\Support\Facades\Log::info('Sending email notification', [
            'to' => $notifiable->email,
            'name' => $notifiable->name,
            'report_type' => $this->reportType,
            'report_name' => ($this->report->reportType ? $this->report->reportType->name : 'Report'),
            'full_report_title' => $fullReportTitle,
            'view_url' => $viewUrl
        ]);

        $mailMessage = (new MailMessage)
                    ->subject("Remarks Added: {$fullReportTitle}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("{$this->adminName} has added remarks to your {$fullReportTitle} submission.")
                    ->line("**Report Details:**")
                    ->line("• Report Type: " . ($this->report->reportType ? $this->report->reportType->name : 'N/A'))
                    ->line("• Period: " . (trim($reportDetails, ' ()') ?: 'Current period'))
                    ->line("• Submitted: " . ($this->report->created_at ? $this->report->created_at->format('M d, Y \a\t g:i A') : 'N/A'))
                    ->line("")
                    ->line("**Remarks from {$this->adminName}:**")
                    ->line("\"{$this->remarks}\"");

        if ($canUpdate) {
            $mailMessage->line("")
                       ->line("✅ **Good news!** You can resubmit this report with the necessary changes.")
                       ->action('Resubmit Report', $viewUrl);
        } else {
            $mailMessage->action('View Report Details', $viewUrl);
        }

        return $mailMessage->line('Please log in to your account to view the complete details and take any necessary action.')
                          ->line('Thank you for your attention to this matter.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reportDetails = '';
        if ($this->reportType === 'weekly' && isset($this->report->month) && isset($this->report->week_number)) {
            $reportDetails = " ({$this->report->month}, Week {$this->report->week_number})";
        } elseif ($this->reportType === 'monthly' && isset($this->report->month)) {
            $reportDetails = " ({$this->report->month})";
        } elseif ($this->reportType === 'quarterly' && isset($this->report->quarter)) {
            $reportDetails = " (Q{$this->report->quarter})";
        } elseif ($this->reportType === 'semestral' && isset($this->report->semester)) {
            $reportDetails = " ({$this->report->semester} Semester)";
        } elseif ($this->reportType === 'annual' && isset($this->report->year)) {
            $reportDetails = " ({$this->report->year})";
        }
        $reportName = $this->report->reportType ? $this->report->reportType->name : 'Report';

        return [
            'report_id' => $this->report->id,
            'report_type' => $this->reportType,
            'report_name' => $reportName,
            'report_details' => $reportDetails,
            'full_report_title' => $reportName . $reportDetails,
            'message' => "{$this->adminName} added remarks to your {$reportName}{$reportDetails} submission.",
            'remarks' => $this->remarks,
            'admin_name' => $this->adminName,
            'timestamp' => now()->toIso8601String(),
            'redirect_url' => url('/barangay/submissions?highlight=' . $this->reportType . '_' . $this->report->id),
            'can_update' => $this->report->can_update ?? false,
            'notification_type' => 'report_remarks',
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Ensure $this->report->user_id is available and valid
        if (!$this->report || !$this->report->user_id) {
            // Log an error or handle appropriately if user_id is missing
            \Illuminate\Support\Facades\Log::error('ReportRemarksNotification: Missing user_id for broadcasting.', ['report_id' => $this->report->id ?? null]);
            return []; // Return empty array or throw exception
        }
        return new PrivateChannel('App.Models.User.' . $this->report->user_id);
    }

    /**
     * Get the type of the broadcast event.
     *
     * @return string
     */
    public function broadcastType()
    {
        return 'report.remarks.added';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // The notifiable entity is implicitly passed to toArray by Laravel if not specified,
        // but for broadcasting, it's better to be explicit about the context.
        // We need to ensure the $notifiable passed to toArray is the intended recipient.
        // $this->report->user should give us the User model instance.
        $user = User::find($this->report->user_id);
        if (!$user) {
             \Illuminate\Support\Facades\Log::error('ReportRemarksNotification: User not found for broadcasting.', ['user_id' => $this->report->user_id]);
             return ['error' => 'User not found for notification data'];
        }
        $data = $this->toArray($user); // Pass the actual notifiable User model.
        return $data; // toArray() already includes 'notification_type' and other necessary fields
    }
}
