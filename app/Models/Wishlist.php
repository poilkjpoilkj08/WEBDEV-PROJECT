<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get total available stock from all store locations for this book
     */
    public function getTotalStockAttribute()
    {
        if (!$this->book) {
            return 0;
        }
        
        // Sum stock from all store locations via pivot table
        return $this->book->storeLocations()
            ->sum('book_store_locations.stock');
    }
}
