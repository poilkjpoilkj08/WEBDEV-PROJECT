<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'isbn',
        'pages',
        'language',
        'publication_year',
        'publisher',
        'status',
        'author_id',
        'category_id',
        'cover_image_url',
        'genres',
        'images',
        'weight_grams',
        'is_featured',
    ];

    protected $casts = [
        'genres'       => 'array',
        'images'       => 'array',
        'price'        => 'decimal:2',
        'weight_grams' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    // ── NEW: store locations relationship ──────────────────────────────────
    public function storeLocations(): BelongsToMany
    {
        return $this->belongsToMany(StoreLocation::class, 'book_store_locations')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getCoverImageUrlAttribute($value): string
    {
        return $value ?: 'https://via.placeholder.com/300x400?text=No+Cover';
    }
}
