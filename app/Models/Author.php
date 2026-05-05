<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Book;

class Author extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'photo_url',
        'publisher',
        'user_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute($value)
    {
        return $value ?: 'https://via.placeholder.com/150x150?text=Author';
    }
}
