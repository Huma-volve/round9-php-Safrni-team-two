<?php

namespace App\Services;

use App\Repositories\Contracts\HotelRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Hotel Service
 * 
 * Handles business logic for hotels.
 * Follows Single Responsibility Principle - only hotel business logic.
 */
class HotelService
{
    public function __construct(
        protected HotelRepositoryInterface $hotelRepository
    ) {}

    /**
     * Get paginated hotels with filters.
     */
    public function getHotels(array $filters = [], int $perPage = 15): array
    {
        try {
            $hotels = $this->hotelRepository->getPaginated($filters, $perPage);

            return [
                'success' => true,
                'data' => $hotels,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching hotels: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch hotels',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get hotel details.
     */
    public function getHotelById(int $id): array
    {
        try {
            $hotel = $this->hotelRepository->findById($id, [
                'activeRooms',
                'activeRooms.seasonalPricing',
            ]);

            if (!$hotel) {
                return [
                    'success' => false,
                    'message' => 'Hotel not found',
                ];
            }

            return [
                'success' => true,
                'data' => $hotel,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching hotel: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch hotel',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get hotel by slug.
     */
    public function getHotelBySlug(string $slug): array
    {
        try {
            $hotel = $this->hotelRepository->findBySlug($slug, [
                'activeRooms',
                'activeRooms.seasonalPricing',
            ]);

            if (!$hotel) {
                return [
                    'success' => false,
                    'message' => 'Hotel not found',
                ];
            }

            return [
                'success' => true,
                'data' => $hotel,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching hotel: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch hotel',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get recommended hotels.
     */
    public function getRecommendedHotels(int $limit = 10): array
    {
        try {
            $hotels = $this->hotelRepository->getRecommended($limit);

            return [
                'success' => true,
                'data' => $hotels,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching recommended hotels: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch recommended hotels',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get featured hotels.
     */
    public function getFeaturedHotels(int $limit = 10): array
    {
        try {
            $hotels = $this->hotelRepository->getFeatured($limit);

            return [
                'success' => true,
                'data' => $hotels,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching featured hotels: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch featured hotels',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Search hotels.
     */
    public function searchHotels(string $term, int $perPage = 15): array
    {
        try {
            $hotels = $this->hotelRepository->search($term, $perPage);

            return [
                'success' => true,
                'data' => $hotels,
            ];
        } catch (\Exception $e) {
            Log::error('Error searching hotels: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to search hotels',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get nearby hotels.
     */
    public function getNearbyHotels(float $latitude, float $longitude, float $radius = 10): array
    {
        try {
            $hotels = $this->hotelRepository->getNearby($latitude, $longitude, $radius);

            return [
                'success' => true,
                'data' => $hotels,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching nearby hotels: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch nearby hotels',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check hotel availability.
     */
    public function checkAvailability(int $hotelId, string $checkIn, string $checkOut): array
    {
        try {
            $isAvailable = $this->hotelRepository->checkAvailability($hotelId, $checkIn, $checkOut);

            $hotel = $this->hotelRepository->findById($hotelId, ['activeRooms']);

            if (!$hotel) {
                return [
                    'success' => false,
                    'message' => 'Hotel not found',
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'hotel' => $hotel,
                    'available' => $isAvailable,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error checking hotel availability: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to check availability',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new hotel.
     */
    public function createHotel(array $data): array
    {
        try {
            DB::beginTransaction();

            $hotel = $this->hotelRepository->create($data);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Hotel created successfully',
                'data' => $hotel,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating hotel: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to create hotel',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update hotel.
     */
    public function updateHotel(int $id, array $data): array
    {
        try {
            DB::beginTransaction();

            $updated = $this->hotelRepository->update($id, $data);

            if (!$updated) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Hotel not found or update failed',
                ];
            }

            DB::commit();

            $hotel = $this->hotelRepository->findById($id);

            return [
                'success' => true,
                'message' => 'Hotel updated successfully',
                'data' => $hotel,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating hotel: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to update hotel',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete hotel.
     */
    public function deleteHotel(int $id): array
    {
        try {
            $deleted = $this->hotelRepository->delete($id);

            if (!$deleted) {
                return [
                    'success' => false,
                    'message' => 'Hotel not found or delete failed',
                ];
            }

            return [
                'success' => true,
                'message' => 'Hotel deleted successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting hotel: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to delete hotel',
                'error' => $e->getMessage(),
            ];
        }
    }
}