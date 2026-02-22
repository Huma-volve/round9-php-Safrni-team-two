<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomBooking;
use App\Repositories\Contracts\RoomBookingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class RoomBookingService
{
    public function __construct(
        protected RoomBookingRepositoryInterface $bookingRepo,
        protected RoomService $roomService,
        protected HotelService $hotelService,
    ) {}

    // =====================================================
    // Create Booking
    // =====================================================

    public function createBooking(int $userId, array $data): array
    {
        try {
            return DB::transaction(function () use ($userId, $data) {

                $room = Room::with('hotel')->lockForUpdate()->find($data['room_id']);

                if (! $room || ! $room->is_active) {
                    return ['success' => false, 'message' => 'Room not found or not available.'];
                }

                $checkIn  = $data['check_in'];
                $checkOut = $data['check_out'];

                // 1. تحقق من التواريخ
                if (Carbon::parse($checkIn)->gte(Carbon::parse($checkOut))) {
                    return ['success' => false, 'message' => 'Check-out must be after check-in.'];
                }

                // 2. تحقق من عدم وجود حجز متداخل (pessimistic lock)
                if ($this->bookingRepo->hasOverlappingBooking($room->id, $checkIn, $checkOut)) {
                    return ['success' => false, 'message' => 'Room is not available for the selected dates.'];
                }

                // 3. تحقق من الـ availability calendar
                $nights     = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
                $roomsCount = $data['rooms_count'] ?? 1;

                if (! $room->isAvailableForRange($checkIn, $checkOut, $roomsCount)) {
                    return ['success' => false, 'message' => 'Not enough rooms available for selected dates.'];
                }

                // 4. حساب السعر
                $pricePerNight = $room->getPriceForDate($checkIn);
                $subtotal      = $pricePerNight * $nights * $roomsCount;
                $taxAmount     = $subtotal * (($room->hotel->tax_percentage ?? 0) / 100);
                $serviceFee    = $room->hotel->service_fee ?? 0;
                $totalAmount   = $subtotal + $taxAmount + $serviceFee;

                // 5. إنشاء الحجز
                $booking = $this->bookingRepo->createBooking([
                    'user_id'          => $userId,
                    'hotel_id'         => $room->hotel_id,
                    'room_id'          => $room->id,
                    'booking_reference' => RoomBooking::generateReference(),
                    'check_in'         => $checkIn,
                    'check_out'        => $checkOut,
                    'nights'           => $nights,
                    'adults'           => $data['adults'] ?? 1,
                    'children'         => $data['children'] ?? 0,
                    'infants'          => $data['infants'] ?? 0,
                    'rooms_count'      => $roomsCount,
                    'price_per_night'  => $pricePerNight,
                    'subtotal'         => $subtotal,
                    'tax_amount'       => $taxAmount,
                    'service_fee'      => $serviceFee,
                    'total_amount'     => $totalAmount,
                    'currency'         => $data['currency'] ?? 'USD',
                    'status'           => 'pending',
                    'payment_status'   => 'unpaid',
                    'guest_info'       => $data['guest_info'] ?? null,
                    'extras'           => $data['extras'] ?? null,
                    'special_requests' => $data['special_requests'] ?? null,
                ]);

                return [
                    'success' => true,
                    'data'    => $booking->load(['hotel', 'room']),
                ];
            });
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()];
        }
    }

    // =====================================================
    // Get User Bookings
    // =====================================================

    public function getUserBookings(int $userId, array $filters = [], int $perPage = 15): array
    {
        $bookings = $this->bookingRepo->getUserBookings($userId, $filters, $perPage);

        return ['success' => true, 'data' => $bookings];
    }

    // =====================================================
    // Get Single Booking
    // =====================================================

    public function getBooking(int $id, int $userId): array
    {
        $booking = $this->bookingRepo->findById($id, ['hotel', 'room', 'reviews']);

        if (! $booking) {
            return ['success' => false, 'message' => 'Booking not found.'];
        }

        // المستخدم يشوف بس حجوزاته
        if ($booking->user_id !== $userId) {
            return ['success' => false, 'message' => 'Unauthorized.'];
        }

        return ['success' => true, 'data' => $booking];
    }

    // =====================================================
    // Cancel Booking
    // =====================================================

    public function cancelBooking(int $id, int $userId, string $reason = ''): array
    {
        $booking = $this->bookingRepo->findById($id);

        if (! $booking) {
            return ['success' => false, 'message' => 'Booking not found.'];
        }

        if ($booking->user_id !== $userId) {
            return ['success' => false, 'message' => 'Unauthorized.'];
        }

        if (! $booking->isCancellable()) {
            return ['success' => false, 'message' => 'This booking cannot be cancelled.'];
        }

        $this->bookingRepo->cancelBooking($id, $reason);

        return ['success' => true, 'message' => 'Booking cancelled successfully.'];
    }
}