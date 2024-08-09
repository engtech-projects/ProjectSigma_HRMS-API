<?php

namespace App\Notifications;

use App\Enums\ApprovalModels;
use App\Models\FailureToLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FailureToLogRequestDenied extends Notification
{
    use Queueable;
    protected $failureToLogRequest;
    /**
     * Create a new notification instance.
     */
    public function __construct(FailureToLog $flogRequest)
    {
        $this->failureToLogRequest = $flogRequest;
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
        return (new MailMessage())
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
            "message" => "Your FAILURE TO LOG REQUEST has been DENIED",
            "type" => ApprovalModels::FailureToLog->name,
            "action_type" => "View",
            "metadata" => $this->failureToLogRequest->toArray(),
        ];
    }
}
