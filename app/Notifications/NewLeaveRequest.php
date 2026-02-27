<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewLeaveRequest extends Notification
{
    use Queueable;

    public function __construct(public Leave $leave) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $user = $this->leave->user()->first();
        $category = $this->leave->category()->first();
        return [
            'title' => 'New leave request',
            'message' => ($user?->display_name ?? 'A user').' requested '.$this->leave->days.' day'.($this->leave->days==1?'':'s').($category ? ' ('.$category->name.')' : ''),
            'leave_id' => $this->leave->id,
            'start_date' => $this->leave->start_date,
            'end_date' => $this->leave->end_date,
            'status' => $this->leave->status,
        ];
    }
}

