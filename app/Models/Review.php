<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'reviewable_id',
        'reviewable_type',
        'booking_id',
        'rating',
        'title',
        'body',
        'photos',
        'helpful_votes',
        'status',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'photos'      => 'array',
        'approved_at' => 'datetime',
        'rating'      => 'integer',
    ];

    // =====================================================
    // Relationships
    // =====================================================

    /**
     * Polymorphic: Hotel, Room, Tour, Car...
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(RoomBooking::class, 'booking_id');
    }

    // =====================================================
    // Scopes
    // =====================================================

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('reviewable_type', $type)
                     ->where('reviewable_id', $id);
    }

    // =====================================================
    // Accessors
    // =====================================================

    public function getPhotosUrlsAttribute(): array
    {
        if (! $this->photos) {
            return [];
        }

        return array_map(fn($p) => asset('storage/' . $p), $this->photos);
    }
}