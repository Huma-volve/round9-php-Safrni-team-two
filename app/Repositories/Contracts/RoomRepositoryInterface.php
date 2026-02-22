<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Room Repository Interface
 */
interface RoomRepositoryInterface
{
    public function getPaginatedForHotel(int $hotelId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id, array $relations = []): ?object;
    public function findBySlug(int $hotelId, string $slug, array $relations = []): ?object;
    public function create(array $data): object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function checkAvailability(int $roomId, string $checkIn, string $checkOut, int $roomsNeeded = 1): array;
    public function getPriceForDateRange(int $roomId, string $checkIn, string $checkOut): float;
}