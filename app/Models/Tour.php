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
        'created_by',
    ];

    protected $casts = [
        'recommended' => 'boolean',
    ];

    // Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tourPriceTier()
    {
        return $this->hasMany(TourPriceTier::class);
    }

    protected $appends = ['adult_price', 'available_slots', 'is_available'];

    protected $hidden = [
        'tour_price_tier_min_adult_price',
        'schedules_sum_available_slots',
    ];

    public function getAdultPriceAttribute()
    {
        return $this->tour_price_tier_min_adult_price;
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->schedules_sum_available_slots ?? 0;
    }

    public function getIsAvailableAttribute()
    {
        return ($this->schedules_sum_available_slots ?? 0) > 0;
    }


    public function schedules()
    {
        return $this->hasMany(TourSchedule::class);
    }

    public function reviews()
{
    return $this->morphMany(Review::class, 'reviewable');
}
}
