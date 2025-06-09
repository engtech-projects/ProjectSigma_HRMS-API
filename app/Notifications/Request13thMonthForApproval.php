<?php

namespace App\Notifications;

use App\Enums\ApprovalModels;
use App\Enums\NotificationModules;
use App\Models\Request13thMonth;
use App\Models\RequestVoid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Request13thMonthForApproval extends Notification
{
    use Queueable;
    protected $notifModel;
    /**
     * Create a new notification instance.
     */
    public function __construct(Request13thMonth $notifModel)
    {
        $this->notifModel = $notifModel;
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
            "message" => "A 13th Month Request needs your approval",
            "type" => ApprovalModels::Request13thMonth->name,
            "action_type" => "Approve",
            "module" => NotificationModules::HRMS->value,
            "metadata" => [
                "id" => $this->notifModel->id,
            ],
        ];
    }
}
