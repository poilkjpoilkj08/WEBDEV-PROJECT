<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StoreLocation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'opening_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_store_locations')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'store_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
