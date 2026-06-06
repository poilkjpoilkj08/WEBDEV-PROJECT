<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BookCategory;
use App\Models\Author;

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
        'publisher_id',
        'cover_type',
        'status',
        'author_id',
        'category_id',
        'cover_image_url',
        'genres',
        'images',
        'weight_grams',
        'is_featured',
        'stock'
    ];

    protected $casts = [
        'genres' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
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

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }

    public function storeLocations(): BelongsToMany
    {
        return $this->belongsToMany(StoreLocation::class, 'book_store_locations')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

    /**
     * Get all order details for this book
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'book_id');
    }

    /**
     * Get total stock from all stores
     * Sums up stock from book_store_locations pivot table
     */
    public function getTotalStockAttribute()
    {
        return $this->storeLocations()
            ->sum('book_store_locations.stock');
    }

    /**
     * Get stock for a specific store
     */
    public function getStockForStore($storeId)
    {
        $pivot = $this->storeLocations()
            ->where('store_location_id', $storeId)
            ->first();
        
        return $pivot ? $pivot->pivot->stock : 0;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    // Accessor for cover image with asset() helper for local paths
    public function getCoverImageSrcAttribute()
    {
        $url = $this->cover_image_url;
        
        if (!$url) {
            return 'https://via.placeholder.com/300x400?text=No+Cover';
        }
        
        // If it's already a full URL (http/https), return as-is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        
        // Otherwise treat as local path and use asset() helper
        return asset($url);
    }

    // Accessor for images array with asset() helper for local paths
    public function getImageUrlsAttribute()
    {
        $images = $this->images ?? [];
        
        return collect($images)
            ->map(function ($url) {
                if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                    return $url;
                }
                return asset($url);
            })
            ->toArray();
    }
}
