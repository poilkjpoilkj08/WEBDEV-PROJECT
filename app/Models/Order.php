<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_name',
        'total_price',
        'status',
        'payment_url',
        'payment_method',
        'paid_at',
        // Shipping fields
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'shipping_country',
        'shipping_method',
        'shipping_cost',
        'shipping_status',
        'tracking_number',
        'shipped_at',
        // Store and distance fields
        'store_id',
        'shipping_distance_km',
    ];

    protected $casts = [
        'paid_at'     => 'datetime',
        'shipped_at'  => 'datetime',
        'shipping_cost' => 'decimal:2',
        'total_price'   => 'decimal:2',
        'shipping_distance_km' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreLocation::class, 'store_id');
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getGrandTotalAttribute()
    {
        return $this->total_price + ($this->shipping_cost ?? 0);
    }
}
