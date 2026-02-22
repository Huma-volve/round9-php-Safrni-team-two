<?php


namespace App\Repositories;

use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Room Repository Implementation
 */
class RoomRepository implements RoomRepositoryInterface
{
    public function __construct(
        protected Room $model
    ) {}

    public function getPaginatedForHotel(int $hotelId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['hotel'])->forHotel($hotelId)->active();

        // Apply filters
        if (!empty($filters['min_price'])) {
            $query->where('base_price_per_night', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('base_price_per_night', '<=', $filters['max_price']);
        }

        if (!empty($filters['adults'])) {
            $query->where('max_adults', '>=', $filters['adults']);
        }

        if (!empty($filters['children'])) {
            $query->where('max_children', '>=', $filters['children']);
        }

        if (!empty($filters['refundable_only'])) {
            $query->refundable();
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'display_order';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function findById(int $id, array $relations = []): ?object
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    public function findBySlug(int $hotelId, string $slug, array $relations = []): ?object
    {
        $query = $this->model->forHotel($hotelId);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->where('slug', $slug)->first();
    }

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $room = $this->model->find($id);
        
        if (!$room) {
            return false;
        }

        return $room->update($data);
    }

    public function delete(int $id): bool
    {
        $room = $this->model->find($id);
        
        if (!$room) {
            return false;
        }

        return $room->delete();
    }

    public function checkAvailability(int $roomId, string $checkIn, string $checkOut, int $roomsNeeded = 1): array
    {
        $room = $this->findById($roomId, ['hotel']);

        if (!$room) {
            return [
                'available' => false,
                'message' => 'Room not found',
            ];
        }

        $isAvailable = $room->isAvailableForRange($checkIn, $checkOut, $roomsNeeded);

        if (!$isAvailable) {
            return [
                'available' => false,
                'message' => 'Room not available for selected dates',
            ];
        }

        $totalPrice = $room->getTotalPriceForRange($checkIn, $checkOut);
        $nights = Carbon::parse($checkIn)->diffInDays($checkOut);

        // Calculate taxes and fees
        $taxAmount = ($totalPrice * $room->hotel->tax_percentage) / 100;
        $serviceFee = $room->hotel->service_fee * $roomsNeeded;
        $grandTotal = $totalPrice + $taxAmount + $serviceFee;

        return [
            'available' => true,
            'room' => $room,
            'pricing' => [
                'nights' => $nights,
                'rooms_count' => $roomsNeeded,
                'subtotal' => round($totalPrice, 2),
                'tax_percentage' => $room->hotel->tax_percentage,
                'tax_amount' => round($taxAmount, 2),
                'service_fee' => round($serviceFee, 2),
                'total' => round($grandTotal, 2),
                'currency' => $room->currency,
            ],
        ];
    }

    public function getPriceForDateRange(int $roomId, string $checkIn, string $checkOut): float
    {
        $room = $this->findById($roomId);

        if (!$room) {
            return 0;
        }

        return $room->getTotalPriceForRange($checkIn, $checkOut);
    }
}