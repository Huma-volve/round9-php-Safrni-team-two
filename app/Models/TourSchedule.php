<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'start_date',
        'end_date',
        'capacity',
        'available_slots',
        'price_tier_id',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function priceTier()
    {
        return $this->belongsTo(TourPriceTier::class);
    }
}
