<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage; // Optional
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\User; // For the submitting user (Barangay) and notifiable user (Facilitator)
// use App\Models\Report; // Assuming you have a unified Report model, or pass specific report model

class NewSubmissionReceivedNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $reportModel; // e.g., WeeklyReport, MonthlyReport, or a general Report model instance
    public $submittingUser;

    /**
     * Create a new notification instance.
     *
     * @param mixed $reportModel The submitted report model instance.
     * @param User $submittingUser The user who submitted the report.
     */
    public function __construct($reportModel, User $submittingUser)
    {
        $this->reportModel = $reportModel;
        $this->submittingUser = $submittingUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Add 'mail' if desired
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) // $notifiable here is the Facilitator/Admin
    {
        $reportName = $this->reportModel->reportType ? $this->reportModel->reportType->name : 'a report';
        $message = "New Submission: {$this->submittingUser->name} (Barangay) has submitted {$reportName}.";
        
        // Determine a redirect URL based on user type
        $redirectUrl = '#'; // Default fallback
        if ($notifiable->user_type === 'facilitator') {
            $redirectUrl = route('facilitator.view-submissions');
        } elseif ($notifiable->user_type === 'admin') {
            $redirectUrl = route('admin.reports.index');
        }
        // Append report ID if available and useful for highlighting
        // $redirectUrl .= '?report_id=' . $this->reportModel->id; 

        return [
            'report_id' => $this->reportModel->id,
            'report_name' => $reportName,
            'submitting_user_id' => $this->submittingUser->id,
            'submitting_user_name' => $this->submittingUser->name,
            'barangay_name' => $this->submittingUser->name,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
            'redirect_url' => $redirectUrl,
            'notification_type' => 'new_submission_received',
            'title' => 'New Report Submission',
            'full_report_title' => "New {$reportName} from {$this->submittingUser->name}",
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // This notification is for a specific user (Facilitator/Admin)
        // The notifiable entity itself will provide the ID when $facilitator->notify() is called.
        return new PrivateChannel('App.Models.User.{id}'); // Laravel replaces {id} with $notifiable->id
    }

    /**
     * Get the type of the broadcast event.
     */
    public function broadcastType()
    {
        return 'submission.new.received';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith() // $notifiable here is the Facilitator/Admin
    {
        // Let Laravel handle passing the notifiable to toArray for broadcast payload.
        return $this->toArray(null); 
    }

    // Optional: toMail method
    /*
    public function toMail($notifiable)
    {
        $reportName = $this->reportModel->reportType ? $this->reportModel->reportType->name : 'a report';
        $subject = "New Report Submission: {$reportName} by {$this->submittingUser->name}";

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A new report has been submitted by {$this->submittingUser->name} (Barangay).")
                    ->line("Report Type: {$reportName}")
                    ->line("Submitted by: {$this->submittingUser->name}")
                    ->action('View Submission', $this->toArray($notifiable)['redirect_url'])
                    ->line("Please review it at your earliest convenience.");
    }
    */
} 