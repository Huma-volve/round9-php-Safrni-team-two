<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;
    protected $fillable = [
        'airport_code',
        'airport_name',
        'city',
        'country',
    ];

    public function departures()
    {
        return $this->hasMany(Flight::class, 'origin_id');
    }

    public function arrivals()
    {
        return $this->hasMany(Flight::class, 'destination_id');
    }
}
