<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBooking extends Model
{
    protected $fillable = [
        'car_id',
        'user_id',
        'pickup_datetime',
        'dropoff_datetime',
        'pickup_location',
        'dropoff_location',
        'total_price',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
