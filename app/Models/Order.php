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
        'payment_processed',
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
        'shipping_zone',
        'shipping_breakdown',
        'shipping_status',
        'tracking_number',
        'shipped_at',
        // Store and distance fields
        'store_id',
        'shipping_distance_km',
        // Delivery confirmation and refund fields
        'delivery_confirmed_at',
        'delivery_confirmation_deadline',
        'delivery_confirmed_by_user',
        'refund_requested_at',
        'refund_status',
        'refund_reason',
        'refund_amount',
        'revenue_recorded',
    ];

    protected $casts = [
        'paid_at'                          => 'datetime',
        'shipped_at'                       => 'datetime',
        'delivery_confirmed_at'            => 'datetime',
        'delivery_confirmation_deadline'   => 'datetime',
        'refund_requested_at'              => 'datetime',
        'shipping_cost'                    => 'decimal:2',
        'refund_amount'                    => 'decimal:2',
        'total_price'                      => 'decimal:2',
        'shipping_distance_km'             => 'decimal:2',
        'delivery_confirmed_by_user'       => 'boolean',
        'revenue_recorded'                 => 'boolean',
        'payment_processed'                => 'boolean',
        'shipping_breakdown'               => 'array',
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

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function getGrandTotalAttribute()
    {
        return $this->total_price + ($this->shipping_cost ?? 0);
    }

    /**
     * Check if delivery confirmation is still pending
     */
    public function isDeliveryConfirmationPending(): bool
    {
        return $this->shipping_status === 'shipped' && !$this->delivery_confirmed_by_user;
    }

    /**
     * Check if delivery confirmation deadline has passed
     */
    public function isDeliveryDeadlinePassed(): bool
    {
        return $this->delivery_confirmation_deadline && now()->isAfter($this->delivery_confirmation_deadline);
    }

    /**
     * Check if refund can be requested
     */
    public function canRequestRefund(): bool
    {
        // Can request refund if:
        // 1. Payment is paid (not pending or cancelled)
        // 2. User hasn't manually confirmed delivery
        // 3. No refund already in progress or completed
        return $this->status === 'paid'
            && !$this->delivery_confirmed_by_user
            && in_array($this->refund_status ?? 'none', ['none', null]);
    }
}

