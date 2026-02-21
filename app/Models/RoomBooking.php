<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'room_id',
        'booking_reference',
        'check_in',
        'check_out',
        'nights',
        'adults',
        'children',
        'infants',
        'rooms_count',
        'price_per_night',
        'subtotal',
        'tax_amount',
        'service_fee',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'guest_info',
        'extras',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'check_in'       => 'date',
        'check_out'      => 'date',
        'cancelled_at'   => 'datetime',
        'guest_info'     => 'array',
        'extras'         => 'array',
        'price_per_night' => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'service_fee'    => 'decimal:2',
        'total_amount'   => 'decimal:2',
    ];

    // =====================================================
    // Relationships
    // =====================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'booking_id');
    }

    // =====================================================
    // Scopes
    // =====================================================

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in', '>=', now()->toDateString())
                     ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // =====================================================
    // Business Logic
    // =====================================================

    /**
     * هل يمكن إلغاء الحجز؟
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->check_in->isFuture();
    }

    /**
     * هل المستخدم أكمل الإقامة؟ (يقدر يعمل review)
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * توليد booking reference فريد
     */
    public static function generateReference(): string
    {
        do {
            $ref = 'HB-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('booking_reference', $ref)->exists());

        return $ref;
    }
}