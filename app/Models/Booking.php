<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'item_id',
        'status',
        'payment_status',
        'total_price',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    // Relations

    public function details()
    {
        return $this->hasOne(BookingDetail::class);
    }
    public function detail()
    {
        return $this->belongsTo(BookingDetail::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPayableAmount(): float
    {
        return (float) $this->total_price;
    }

    // Mark booking as paid
    public function markAsPaid(): void
    {
        $this->update([
            'status' => $this->status,
            'payment_status' => $this->payment_status,
        ]);
    }
}
