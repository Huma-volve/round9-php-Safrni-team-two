<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'aircraft_id',
        'row_number',
        'column_letter',
        'class_type',
        'seat_position',
        'status',
    ];

    protected $casts = [
        'row_number' => 'integer',
    ];

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    // Seat Number (12A)
    public function getSeatNumberAttribute()
    {
        return $this->row_number . $this->column_letter;
    }
}
