<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportRemarksNotification extends Notification
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
            'report_id' => $report->id,
            'report_type' => $reportType,
            'report_name' => $report->reportType->name,
            'remarks' => $remarks,
            'admin_name' => $adminName
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
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

        $fullReportTitle = $this->report->reportType->name . $reportDetails;
        $canUpdate = $this->report->can_update ?? false;
        $viewUrl = url('/barangay/submissions');

        // Log email being sent
        \Illuminate\Support\Facades\Log::info('Sending email notification', [
            'to' => $notifiable->email,
            'name' => $notifiable->name,
            'report_type' => $this->reportType,
            'report_name' => $this->report->reportType->name,
            'full_report_title' => $fullReportTitle,
            'view_url' => $viewUrl
        ]);

        $mailMessage = (new MailMessage)
                    ->subject("Remarks Added: {$fullReportTitle}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("{$this->adminName} has added remarks to your {$fullReportTitle} submission.")
                    ->line("**Report Details:**")
                    ->line("• Report Type: {$this->report->reportType->name}")
                    ->line("• Period: " . (trim($reportDetails, ' ()') ?: 'Current period'))
                    ->line("• Submitted: " . $this->report->created_at->format('M d, Y \a\t g:i A'))
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
        // Get additional report details for better context
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

        return [
            'report_id' => $this->report->id,
            'report_type' => $this->reportType,
            'report_name' => $this->report->reportType->name,
            'report_details' => $reportDetails,
            'full_report_title' => $this->report->reportType->name . $reportDetails,
            'remarks' => $this->remarks,
            'admin_name' => $this->adminName,
            'timestamp' => now()->toIso8601String(),
            'redirect_url' => url('/barangay/submissions?highlight=' . $this->reportType . '_' . $this->report->id),
            'can_update' => $this->report->can_update ?? false
        ];
    }
}
