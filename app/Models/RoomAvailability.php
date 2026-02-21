<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Room Availability Model
 * 
 * Manages daily availability and pricing for rooms.
 */
class RoomAvailability extends Model
{
    use HasFactory;

    protected $table = 'room_availability';

    protected $fillable = [
        'room_id',
        'date',
        'available_rooms',
        'price_per_night',
        'is_blocked',
        'block_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'price_per_night' => 'decimal:2',
        'is_blocked' => 'boolean',
    ];

    /**
     * Get the room that owns this availability.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope to filter by date.
     */
    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope to filter available (not blocked).
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_blocked', false)
            ->where('available_rooms', '>', 0);
    }

    /**
     * Scope to filter blocked.
     */
    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    /**
     * Check if date is available.
     */
    public function isAvailable(): bool
    {
        return !$this->is_blocked && $this->available_rooms > 0;
    }

    /**
     * Decrease available rooms.
     */
    public function decreaseAvailability(int $quantity = 1): bool
    {
        if ($this->available_rooms < $quantity) {
            return false;
        }

        $this->decrement('available_rooms', $quantity);
        return true;
    }

    /**
     * Increase available rooms.
     */
    public function increaseAvailability(int $quantity = 1): bool
    {
        $this->increment('available_rooms', $quantity);
        return true;
    }
}


