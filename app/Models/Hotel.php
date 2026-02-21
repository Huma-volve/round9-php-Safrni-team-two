<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasImages;

/**
 * Hotel Model
 * 
 * Represents a hotel entity with its properties and relationships.
 * Follows Single Responsibility Principle - handles only hotel data.
 */
class Hotel extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasImages;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'full_description',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'main_image',
        'gallery',
        'star_rating',
        'overall_rating',
        'total_reviews',
        'amenities',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'policies',
        'phone',
        'email',
        'website',
        'contact_info',
        'tax_percentage',
        'service_fee',
        'is_recommended',
        'is_featured',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gallery' => 'array',
        'amenities' => 'array',
        'policies' => 'array',
        'contact_info' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'overall_rating' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'is_recommended' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'average_price',
        'main_image_url',
        'gallery_urls',
    ];

    // =====================================================
    // Relationships
    // =====================================================

    /**
     * Get the rooms for the hotel.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get active rooms only.
     */
    public function activeRooms(): HasMany
    {
        return $this->rooms()->where('is_active', true);
    }

    /**
     * Get the user who created the hotel.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the hotel.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =====================================================
    // Scopes (Query Filters)
    // =====================================================

    /**
     * Scope to filter active hotels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by city.
     */
    public function scopeInCity($query, string $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope to filter recommended hotels.
     */
    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    /**
     * Scope to filter featured hotels.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by minimum rating.
     */
    public function scopeMinRating($query, float $rating)
    {
        return $query->where('overall_rating', '>=', $rating);
    }

    /**
     * Scope for search by name or city.
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('city', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%");
        });
    }

    // =====================================================
    // Accessors (Computed Properties)
    // =====================================================

    /**
     * Get the average price from rooms.
     */
    public function getAveragePriceAttribute(): float
    {
        return $this->rooms()->avg('base_price_per_night') ?? 0;
    }

    /**
     * Get the main image URL.
     */
    public function getMainImageUrlAttribute(): string
    {
        return $this->main_image 
            ? asset('storage/' . $this->main_image) 
            : asset('images/hotel-placeholder.jpg');
    }

    /**
     * Get all gallery image URLs.
     */
    public function getGalleryUrlsAttribute(): array
    {
        if (!$this->gallery) {
            return [];
        }

        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $this->gallery);
    }

    // =====================================================
    // Business Logic Methods
    // =====================================================

    /**
     * Update the hotel's overall rating.
     * 
     * This should be called after reviews are added/updated.
     */
    public function recalculateRating(): void
    {
        // This will be implemented when Review model is created
        // For now, it's a placeholder following Open/Closed Principle
    }

    /**
     * Check if hotel is available for given date range.
     */
    public function isAvailableForDateRange(string $checkIn, string $checkOut): bool
    {
        return $this->activeRooms()
            ->whereHas('availability', function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('date', [$checkIn, $checkOut])
                    ->where('available_rooms', '>', 0)
                    ->where('is_blocked', false);
            })
            ->exists();
    }
}