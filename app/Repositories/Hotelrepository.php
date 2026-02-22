<?php

namespace App\Repositories;

use App\Models\Hotel;
use App\Repositories\Contracts\HotelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Hotel Repository
 * 
 * Implements data access logic for hotels.
 * Follows Repository Pattern and Single Responsibility Principle.
 */
class HotelRepository implements HotelRepositoryInterface
{
    /**
     * Create a new repository instance.
     */
    public function __construct(
        protected Hotel $model
    ) {}

    /**
     * Get paginated hotels with filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['activeRooms'])->active();

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find hotel by ID.
     */
    public function findById(int $id, array $relations = []): ?object
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    /**
     * Find hotel by slug.
     */
    public function findBySlug(string $slug, array $relations = []): ?object
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->where('slug', $slug)->first();
    }

    /**
     * Create a new hotel.
     */
    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    /**
     * Update hotel.
     */
    public function update(int $id, array $data): bool
    {
        $hotel = $this->model->find($id);
        
        if (!$hotel) {
            return false;
        }

        return $hotel->update($data);
    }

    /**
     * Delete hotel.
     */
    public function delete(int $id): bool
    {
        $hotel = $this->model->find($id);
        
        if (!$hotel) {
            return false;
        }

        return $hotel->delete();
    }

    /**
     * Get recommended hotels.
     */
    public function getRecommended(int $limit = 10): Collection
    {
        return $this->model->with(['activeRooms'])
            ->active()
            ->recommended()
            ->orderBy('overall_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured hotels.
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->with(['activeRooms'])
            ->active()
            ->featured()
            ->orderBy('overall_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search hotels.
     */
    public function search(string $term, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['activeRooms'])
            ->active()
            ->search($term)
            ->paginate($perPage);
    }

    /**
     * Get hotels by city.
     */
    public function getByCity(string $city, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['activeRooms'])
            ->active()
            ->inCity($city)
            ->paginate($perPage);
    }

    /**
     * Get nearby hotels.
     */
    public function getNearby(float $latitude, float $longitude, float $radiusKm = 10): Collection
    {
        return $this->model->selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
        ->active()
        ->having('distance', '<=', $radiusKm)
        ->orderBy('distance')
        ->get();
    }

    /**
     * Check hotel availability.
     */
    public function checkAvailability(int $hotelId, string $checkIn, string $checkOut): bool
    {
        $hotel = $this->findById($hotelId, ['activeRooms']);
        
        if (!$hotel) {
            return false;
        }

        return $hotel->isAvailableForDateRange($checkIn, $checkOut);
    }

    /**
     * Apply filters to query.
     */
    protected function applyFilters($query, array $filters)
    {
        // Search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // City filter
        if (!empty($filters['city'])) {
            $query->inCity($filters['city']);
        }

        // Rating filter
        if (!empty($filters['min_rating'])) {
            $query->minRating($filters['min_rating']);
        }

        // Star rating filter
        if (!empty($filters['stars'])) {
            $stars = is_array($filters['stars']) ? $filters['stars'] : [$filters['stars']];
            $query->whereIn('star_rating', $stars);
        }

        // Price range filter (based on rooms)
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->whereHas('activeRooms', function ($q) use ($filters) {
                if (!empty($filters['min_price'])) {
                    $q->where('base_price_per_night', '>=', $filters['min_price']);
                }
                if (!empty($filters['max_price'])) {
                    $q->where('base_price_per_night', '<=', $filters['max_price']);
                }
            });
        }

        // Recommended filter
        if (!empty($filters['recommended'])) {
            $query->recommended();
        }

        // Featured filter
        if (!empty($filters['featured'])) {
            $query->featured();
        }

        return $query;
    }
}