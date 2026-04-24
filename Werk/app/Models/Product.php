<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    //
    use SoftDeletes;
    public function product_category():BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
