<?php

namespace App\Repositories\Contracts;

use App\Models\RoomBooking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoomBookingRepositoryInterface
{
    public function createBooking(array $data): RoomBooking;

    public function findById(int $id, array $relations = []): ?RoomBooking;

    public function findByReference(string $reference): ?RoomBooking;

    public function getUserBookings(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function cancelBooking(int $id, string $reason): bool;

    public function updateStatus(int $id, string $status): bool;

    public function updatePaymentStatus(int $id, string $paymentStatus): bool;

    public function hasOverlappingBooking(int $roomId, string $checkIn, string $checkOut, ?int $excludeId = null): bool;
}