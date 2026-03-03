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
        $role = (string)($notifiable->role ?? '');
        if (in_array($role, ['hr','dc','rd','ard','admin'], true)) {
            return ['database'];
        }
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
            ->action('Download PDF', route('leaves.pdf', $this->leave))
            ->line('Leave ID: '.$this->leave->id);
    }
}
