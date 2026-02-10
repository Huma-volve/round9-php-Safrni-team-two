<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'main_image',
        'duration',
        'location',
        'stars',
        'recommended',
        // 'created_by',
    ];

    protected $casts = [
        'recommended' => 'boolean',
    ];

    // Relations
    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    public function tourPriceTier()
    {
        return $this->hasMany(TourPriceTier::class);
    }

    protected $appends = ['adult_price'];
    protected $hidden = [
        'tour_price_tier_min_adult_price',
    ];

    public function getAdultPriceAttribute()
    {
        return $this->tour_price_tier_min_adult_price;
    }
}
