<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'default_credits', 'vl_default_credits', 'sl_default_credits'];
}
