<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStageNotification extends Notification
{
    use Queueable;

    public function __construct(public Leave $leave, public string $title, public string $message) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'leave_id' => $this->leave->id,
            'status' => $this->leave->status,
            'workflow_state' => $this->leave->workflow_state,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->message)
            ->action('Open Dashboard', url('/'))
            ->line('Leave ID: '.$this->leave->id);
    }
}

