<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'email',
        'name',
        'plan',
        'status',
        'token',
        'subscribed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'cancelled_at'  => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Generate a unique unsubscribe token
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
