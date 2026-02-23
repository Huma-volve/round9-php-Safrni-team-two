<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarReview extends Model
{
    protected $table = 'car_reviews';
    protected $fillable = [
        'car_id',
        'user_id',
        'car_booking_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
