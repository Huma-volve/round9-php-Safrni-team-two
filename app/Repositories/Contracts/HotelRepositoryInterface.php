<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Hotel Repository Interface
 * 
 * Defines contract for hotel data access.
 * Follows Interface Segregation and Dependency Inversion principles.
 */
interface HotelRepositoryInterface
{
    /**
     * Get all hotels with pagination and filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find hotel by ID.
     */
    public function findById(int $id, array $relations = []): ?object;

    /**
     * Find hotel by slug.
     */
    public function findBySlug(string $slug, array $relations = []): ?object;

    /**
     * Create a new hotel.
     */
    public function create(array $data): object;

    /**
     * Update hotel.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete hotel.
     */
    public function delete(int $id): bool;

    /**
     * Get recommended hotels.
     */
    public function getRecommended(int $limit = 10): Collection;

    /**
     * Get featured hotels.
     */
    public function getFeatured(int $limit = 10): Collection;

    /**
     * Search hotels by term.
     */
    public function search(string $term, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get hotels by city.
     */
    public function getByCity(string $city, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get nearby hotels.
     */
    public function getNearby(float $latitude, float $longitude, float $radiusKm = 10): Collection;

    /**
     * Check hotel availability for date range.
     */
    public function checkAvailability(int $hotelId, string $checkIn, string $checkOut): bool;
}