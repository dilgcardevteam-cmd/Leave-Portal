<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveApprovalLog extends Model
{
    protected $fillable = [
        'leave_id',
        'step',
        'action',
        'comment',
        'acted_by',
        'acted_at',
        'meta',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
        'meta' => 'array',
    ];

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }
}

