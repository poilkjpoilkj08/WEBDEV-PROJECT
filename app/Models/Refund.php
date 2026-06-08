<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'image_path',
        'amount',
        'status',
        'admin_notes',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the image URL for this refund's evidence
     * Returns null if image doesn't exist
     */
    public function getImageUrl(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        try {
            // Check if file exists in public folder
            $filePath = public_path($this->image_path);
            if (file_exists($filePath)) {
                // Return public URL (no symlink needed)
                return '/' . $this->image_path;
            }
        } catch (\Exception $e) {
            \Log::warning('Refund image URL generation failed', [
                'refund_id' => $this->id,
                'image_path' => $this->image_path,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }
}
