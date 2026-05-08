<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
=======
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BookCategory;
use App\Models\Author;
>>>>>>> 0baef88919b77deb93dd0969a66df6291b15cee3

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
<<<<<<< HEAD
        'is_featured',
    ];

    protected $casts = [
        'genres'       => 'array',
        'images'       => 'array',
        'price'        => 'decimal:2',
=======
        'is_featured'
    ];

    protected $casts = [
        'genres' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
>>>>>>> 0baef88919b77deb93dd0969a66df6291b15cee3
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

<<<<<<< HEAD
    // ── NEW: store locations relationship ──────────────────────────────────
    public function storeLocations(): BelongsToMany
    {
        return $this->belongsToMany(StoreLocation::class, 'book_store_locations')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

=======
>>>>>>> 0baef88919b77deb93dd0969a66df6291b15cee3
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

<<<<<<< HEAD
    public function getFormattedPriceAttribute(): string
=======
    public function getFormattedPriceAttribute()
>>>>>>> 0baef88919b77deb93dd0969a66df6291b15cee3
    {
        return '$' . number_format($this->price, 2);
    }

<<<<<<< HEAD
    public function getCoverImageUrlAttribute($value): string
=======
    public function getCoverImageUrlAttribute($value)
>>>>>>> 0baef88919b77deb93dd0969a66df6291b15cee3
    {
        return $value ?: 'https://via.placeholder.com/300x400?text=No+Cover';
    }
}
