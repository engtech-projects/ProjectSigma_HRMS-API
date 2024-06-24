<?php

namespace App\Notifications;

use App\Enums\ApprovalModels;
use App\Models\EmployeeLeaves;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestForApproval extends Notification
{
    use Queueable;

    private $leaveRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeLeaves $lreq)
    {
        $this->leaveRequest = $lreq;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'database',
            // 'mail',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message" => "A LEAVE REQUEST is for your approval",
            "type" => ApprovalModels::LeaveEmployeeRequest->name,,
            "action_type" => "Approve",
            "metadata" => $this->leaveRequest->toArray(),
        ];
    }
}
