<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Room Seasonal Pricing Model
 * 
 * Manages seasonal pricing for rooms.
 */
class RoomSeasonalPricing extends Model
{
    use HasFactory;

    protected $table = 'room_seasonal_pricing';

    protected $fillable = [
        'room_id',
        'name',
        'start_date',
        'end_date',
        'price_per_night',
        'min_nights',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price_per_night' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the room that owns this pricing.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope to filter active pricing.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    /**
     * Check if pricing is valid for a date.
     */
    public function isValidForDate(string $date): bool
    {
        $carbonDate = \Carbon\Carbon::parse($date);
        
        return $this->is_active 
            && $carbonDate->between($this->start_date, $this->end_date);
    }

    /**
     * Get price for this season.
     */
    public function getPrice(): float
    {
        return (float) $this->price_per_night;
    }
}