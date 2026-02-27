<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'user_id',
        'leave_category_id',
        'start_date',
        'end_date',
        'days',
        'reason',
        'details_json',
        'status',
        'approved_by',
        'approved_at',
        'workflow_state',
        'hr_approved_by','hr_approved_at','hr_comment',
        'dc_approved_by','dc_approved_at','dc_comment',
        'final_approved_by','final_approved_at','final_approver_role','final_comment','final_pdf_path',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
        'dc_approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
        'details_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(LeaveCategory::class, 'leave_category_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
