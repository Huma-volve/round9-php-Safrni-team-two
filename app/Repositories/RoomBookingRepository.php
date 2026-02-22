<?php

namespace App\Repositories;

use App\Models\RoomBooking;
use App\Repositories\Contracts\RoomBookingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RoomBookingRepository implements RoomBookingRepositoryInterface
{
    public function createBooking(array $data): RoomBooking
    {
        return RoomBooking::create($data);
    }

    public function findById(int $id, array $relations = []): ?RoomBooking
    {
        return RoomBooking::with($relations)->find($id);
    }

    public function findByReference(string $reference): ?RoomBooking
    {
        return RoomBooking::with(['hotel', 'room', 'user'])
            ->where('booking_reference', $reference)
            ->first();
    }

    public function getUserBookings(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = RoomBooking::with(['hotel', 'room'])
            ->where('user_id', $userId)
            ->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function cancelBooking(int $id, string $reason): bool
    {
        return (bool) RoomBooking::where('id', $id)->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at'        => now(),
        ]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return (bool) RoomBooking::where('id', $id)->update(['status' => $status]);
    }

    public function updatePaymentStatus(int $id, string $paymentStatus): bool
    {
        return (bool) RoomBooking::where('id', $id)->update(['payment_status' => $paymentStatus]);
    }

    /**
     * تحقق من وجود حجز متداخل لنفس الغرفة
     */
    public function hasOverlappingBooking(int $roomId, string $checkIn, string $checkOut, ?int $excludeId = null): bool
    {
        $query = RoomBooking::where('room_id', $roomId)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out', [$checkIn, $checkOut])
                  ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in', '<=', $checkIn)
                         ->where('check_out', '>=', $checkOut);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}