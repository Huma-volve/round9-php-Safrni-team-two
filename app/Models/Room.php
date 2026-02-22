<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasImages;
use Carbon\Carbon;

/**
 * Room Model
 * 
 * Represents a room type within a hotel.
 * Follows Single Responsibility Principle.
 */
class Room extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasImages;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'hotel_id',
        'name',
        'slug',
        'description',
        'photos',
        'max_adults',
        'max_children',
        'max_infants',
        'total_occupancy',
        'bed_type',
        'number_of_beds',
        'room_area',
        'room_area_unit',
        'base_price_per_night',
        'currency',
        'total_rooms',
        'is_refundable',
        'amenities',
        'extras',
        'is_active',
        'display_order',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'photos' => 'array',
        'amenities' => 'array',
        'extras' => 'array',
        'base_price_per_night' => 'decimal:2',
        'room_area' => 'decimal:2',
        'is_refundable' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'photos_urls',
        'full_name',
    ];

    // =====================================================
    // Relationships
    // =====================================================

    /**
     * Get the hotel that owns the room.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get seasonal pricing for this room.
     */
    public function seasonalPricing(): HasMany
    {
        return $this->hasMany(RoomSeasonalPricing::class);
    }

    /**
     * Get active seasonal pricing.
     */
    public function activeSeasonalPricing(): HasMany
    {
        return $this->seasonalPricing()->where('is_active', true);
    }

    /**
     * Get availability calendar.
     */
    public function availability(): HasMany
    {
        return $this->hasMany(RoomAvailability::class);
    }

    /**
     * Get the user who created the room.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the room.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =====================================================
    // Scopes
    // =====================================================

    /**
     * Scope to filter active rooms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter rooms by hotel.
     */
    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    /**
     * Scope to filter by occupancy capacity.
     */
    public function scopeCanAccommodate($query, int $adults, int $children = 0, int $infants = 0)
    {
        return $query->where('max_adults', '>=', $adults)
            ->where('max_children', '>=', $children)
            ->where('max_infants', '>=', $infants);
    }

    /**
     * Scope to filter by price range.
     */
    public function scopePriceBetween($query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('base_price_per_night', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to filter refundable rooms.
     */
    public function scopeRefundable($query)
    {
        return $query->where('is_refundable', true);
    }

    // =====================================================
    // Accessors
    // =====================================================

    /**
     * Get all photo URLs.
     */
    public function getPhotosUrlsAttribute(): array
    {
        if (!$this->photos) {
            return [];
        }

        return array_map(function ($photo) {
            return asset('storage/' . $photo);
        }, $this->photos);
    }

    /**
     * Get full room name with hotel.
     */
    public function getFullNameAttribute(): string
    {
        return $this->hotel 
            ? "{$this->hotel->name} - {$this->name}" 
            : $this->name;
    }

    // =====================================================
    // Business Logic Methods
    // =====================================================

    /**
     * Get price for a specific date.
     * 
     * Checks seasonal pricing first, then availability calendar, 
     * finally falls back to base price.
     */
    public function getPriceForDate(string $date): float
    {
        $carbonDate = Carbon::parse($date);

        // Check seasonal pricing
        $seasonalPrice = $this->activeSeasonalPricing()
            ->where('start_date', '<=', $carbonDate)
            ->where('end_date', '>=', $carbonDate)
            ->first();

        if ($seasonalPrice) {
            return (float) $seasonalPrice->price_per_night;
        }

        // Check availability calendar for custom pricing
        $availability = $this->availability()
            ->where('date', $carbonDate->format('Y-m-d'))
            ->first();

        if ($availability && $availability->price_per_night) {
            return (float) $availability->price_per_night;
        }

        // Return base price
        return (float) $this->base_price_per_night;
    }

    /**
     * Get total price for a date range.
     */
    public function getTotalPriceForRange(string $checkIn, string $checkOut): float
    {
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        $totalPrice = 0;

        $currentDate = $checkInDate->copy();
        while ($currentDate->lt($checkOutDate)) {
            $totalPrice += $this->getPriceForDate($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        return $totalPrice;
    }

    /**
     * Check if room is available for date range.
     */
    public function isAvailableForRange(string $checkIn, string $checkOut, int $roomsNeeded = 1): bool
    {
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);

        // Get all dates in range
        $dates = [];
        $currentDate = $checkInDate->copy();
        while ($currentDate->lt($checkOutDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Check each date
        foreach ($dates as $date) {
            $available = $this->getAvailableRoomsForDate($date);
            if ($available < $roomsNeeded) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get number of available rooms for a specific date.
     */
    public function getAvailableRoomsForDate(string $date): int
    {
        $availability = $this->availability()
            ->where('date', $date)
            ->first();

        if ($availability) {
            if ($availability->is_blocked) {
                return 0;
            }
            return $availability->available_rooms;
        }

        // If no availability record, assume all rooms are available
        return $this->total_rooms;
    }
}