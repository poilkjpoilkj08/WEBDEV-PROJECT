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
    'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
