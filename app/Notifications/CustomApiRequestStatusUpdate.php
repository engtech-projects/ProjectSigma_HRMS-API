<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomApiRequestStatusUpdate extends Notification
{
    use Queueable;

    protected $module;
    protected $action;
    protected $message;
    protected $requestId;
    protected $requestType;
    /**
     * Create a new notification instance.
     */
    public function __construct($module, $action, $message, $requestId, $requestType)
    {
        $this->module = $module;
        $this->action = $action;
        $this->message = $message;
        $this->requestId = $requestId;
        $this->requestType = $requestType;
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
            "module" => $this->module,
            "action_type" => $this->action,
            "message" => $this->message,
            "type" => $this->requestType,
            "metadata" => [
                "id" => $this->requestId,
            ],
        ];
    }
}
