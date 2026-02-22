<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aircraft extends Model
{
    use HasFactory;
    protected $table = 'aircrafts';
    protected $fillable = [
        'model',
        'code',
        'total_seats',
    ];

    protected $casts = [
        'total_seats' => 'integer',
    ];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}
