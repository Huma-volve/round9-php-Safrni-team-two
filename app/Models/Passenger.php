<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'title',
        'first_name',
        'last_name',
        'date_of_birth',
        'passport_number',
        'nationality',
        'special_requests',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->title} {$this->first_name} {$this->last_name}";
    }
}
