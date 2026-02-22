<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;
    protected $fillable = [
        'flight_number',
        'carrier',
        'origin_id',
        'destination_id',
        'aircraft_id',
        'departure_time',
        'arrival_time',
        'refundability',
        'status'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time'   => 'datetime',
        'refundability'  => 'boolean',
    ];

    public function origin()
    {
        return $this->belongsTo(Airport::class, 'origin_id');
    }

    public function destination()
    {
        return $this->belongsTo(Airport::class, 'destination_id');
    }

    public function fares()
    {
        return $this->hasMany(FlightFare::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeSearch($query, $originId, $destinationId, $date)
    {
        return $query->where('origin_id', $originId)
                     ->where('destination_id', $destinationId)
                     ->whereDate('departure_time', $date);
    }
}
