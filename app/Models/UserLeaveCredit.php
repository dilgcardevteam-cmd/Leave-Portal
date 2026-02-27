<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLeaveCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vl_total',
        'sl_total',
        'credits_total',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
