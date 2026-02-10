<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_reference',
        'contact_email',
        'contact_phone',
        'total_price',
        'tax_amount',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'tax_amount'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Usually a booking is for one flight, but it could be multi-leg.
    // For simplicity, we can get flights via tickets.
    public function flights()
    {
        return $this->hasManyThrough(Flight::class, Ticket::class, 'booking_id', 'id', 'id', 'flight_id')->distinct();
    }
}
