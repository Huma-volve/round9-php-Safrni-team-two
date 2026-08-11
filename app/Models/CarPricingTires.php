<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPricingTires extends Model
{
    protected $table = 'car_pricing_tires';
    protected $guarded = [];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
