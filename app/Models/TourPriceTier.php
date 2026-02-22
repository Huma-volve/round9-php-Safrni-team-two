<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPriceTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'name',
        'adult_price',
        'child_price',
        'infant_price',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
