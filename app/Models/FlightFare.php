<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightFare extends Model
{
    use HasFactory;
    protected $fillable = [
        'flight_id',
        'class_type',
        'base_price',
        'taxes',
        'baggage_price',
        'seats_available',
        'is_refundable',
        'stops'
    ];

    protected $casts = [
        'base_price'     => 'float',
        'taxes'          => 'float',
        'baggage_price'  => 'float',
        'seats_available' => 'integer',
        'is_refundable'  => 'boolean',
        'stops'          => 'integer',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->base_price + $this->taxes + $this->baggage_price;
    }
}
