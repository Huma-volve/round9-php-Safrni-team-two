<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'passenger_id',
        'flight_id',
        'seat_id',
        'ticket_number',
        'class_type',
        'price_paid',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
