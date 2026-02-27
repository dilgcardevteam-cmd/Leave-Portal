<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function getDisplayNameAttribute(): string
    {
        $nameColumn = trim((string)($this->attributes['name'] ?? ''));
        if ($nameColumn !== '') {
            return $nameColumn;
        }
        $parts = [];
        if (!empty($this->first_name)) $parts[] = $this->first_name;
        if (!empty($this->middle_name)) $parts[] = mb_substr($this->middle_name, 0, 1).'.';
        if (!empty($this->last_name)) $parts[] = $this->last_name;
        return trim(implode(' ', $parts));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'email',
        'mobile_number',
        'sex',
        'region',
        'province_office',
        'position',
        'signature_path',
        'salary',
        'vl_total',
        'sl_total',
        'credits_total',
        'id_no',
        'role',
        'otp_code',
        'otp_expires_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'vl_total' => 'decimal:3',
            'sl_total' => 'decimal:3',
            'credits_total' => 'decimal:3',
        ];
    }

    public function credits()
    {
        return $this->hasOne(UserLeaveCredit::class);
    }
}
