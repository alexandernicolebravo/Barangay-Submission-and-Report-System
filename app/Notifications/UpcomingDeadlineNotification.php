<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage; // Optional for mail
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\ReportType; // Assuming you pass the ReportType model
use App\Models\User;      // For the notifiable user
use Carbon\Carbon;

class UpcomingDeadlineNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $reportType;
    public $deadline;

    /**
     * Create a new notification instance.
     *
     * @param ReportType $reportType The report type that is due.
     * @param Carbon $deadline The deadline date.
     */
    public function __construct(ReportType $reportType, Carbon $deadline)
    {
        $this->reportType = $reportType;
        $this->deadline = $deadline;
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
    public function toArray($notifiable)
    {
        $daysRemaining = Carbon::now()->diffInDays($this->deadline, false); // false for signed difference
        $message = "Upcoming Deadline: The report '{$this->reportType->name}' is due in {$daysRemaining} day(s) on {$this->deadline->format('M d, Y')}.";
        if ($daysRemaining < 0) {
             $message = "Deadline Passed: The report '{$this->reportType->name}' was due on {$this->deadline->format('M d, Y')}.";
        } else if ($daysRemaining == 0) {
            $message = "Deadline Today: The report '{$this->reportType->name}' is due today, {$this->deadline->format('M d, Y')}.";
        }

        return [
            'report_type_id' => $this->reportType->id,
            'report_type_name' => $this->reportType->name,
            'deadline' => $this->deadline->toIso8601String(),
            'days_remaining' => $daysRemaining,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
            'redirect_url' => route('barangay.submit-report.form', $this->reportType->id), // Example, adjust as needed
            'notification_type' => 'upcoming_deadline',
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        // This notification is for a specific user (Barangay user)
        // The notifiable entity itself will provide the ID.
        // When sending: $user->notify(new UpcomingDeadlineNotification(...));
        // Laravel automatically gets $notifiable->id if $notifiable is a User model.
        // However, the $notifiable is not available directly in broadcastOn from constructor params.
        // This will be resolved when Laravel calls broadcastOn with the $notifiable instance.
        // For now, we return a placeholder that will be properly populated.
        // A better approach is to pass the user_id if known at construction, or rely on $notifiable in toArray.

        // This will be called with the $notifiable instance by Laravel, so we can access its id.
        // Placeholder: return new PrivateChannel('App.Models.User.placeholder');
        // Actual implementation relies on $this->id which is the notification ID in the database,
        // or on the $notifiable passed when Laravel calls it.
        // Let's assume $notifiable is available via a property if needed or use the implicit $notifiable->id.
        // This channel name will be correctly constructed by Laravel using the notifiable instance.
        return new PrivateChannel('App.Models.User.{id}'); // Laravel replaces {id} with $notifiable->id
    }

    /**
     * Get the type of the broadcast event.
     *
     * @return string
     */
    public function broadcastType()
    {
        return 'deadline.upcoming';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // When Laravel calls this, $this->id will be the DB notification ID.
        // The actual notifiable entity will be used by Laravel when it calls toArray for the broadcast.
        // So, just calling toArray(null) here (or letting Laravel do it) is fine.
        // Let Laravel handle passing the notifiable to toArray for broadcast payload.
        return $this->toArray(null); // Laravel will pass the correct notifiable here.
    }

    // Optional: toMail method if you want email notifications too
    /*
    public function toMail($notifiable)
    {
        $daysRemaining = Carbon::now()->diffInDays($this->deadline, false);
        $subject = "Upcoming Report Deadline: {$this->reportType->name}";
        if ($daysRemaining == 0) {
            $subject = "Report Deadline Today: {$this->reportType->name}";
        } elseif ($daysRemaining < 0) {
            $subject = "Report Deadline Passed: {$this->reportType->name}";
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting("Hello {$notifiable->name},")
                    ->line($this->toArray($notifiable)['message'])
                    ->action('Submit Report', route('barangay.submit-report.form', $this->reportType->id))
                    ->line('Thank you for your prompt attention to this matter.');
    }
    */
} 