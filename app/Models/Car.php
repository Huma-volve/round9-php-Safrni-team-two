<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\CarPricingTier;
use App\Models\CarReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'air_conditioning' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews()
    {
        return $this->hasMany(CarReview::class);
    }

    public function pricingTiers()
    {
        return $this->hasMany(CarPricingTires::class, 'car_id');
    }

    public function favorites()
    {
        return $this->hasMany(CarFavourire::class);
    }
    
}
