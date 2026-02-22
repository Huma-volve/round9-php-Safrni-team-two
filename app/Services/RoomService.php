<?php

namespace App\Services;

use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Room Service
 * 
 * Handles business logic for rooms.
 */
class RoomService
{
    public function __construct(
        protected RoomRepositoryInterface $roomRepository
    ) {}

    /**
     * Get rooms for a hotel.
     */
    public function getRoomsForHotel(int $hotelId, array $filters = [], int $perPage = 15): array
    {
        try {
            $rooms = $this->roomRepository->getPaginatedForHotel($hotelId, $filters, $perPage);

            return [
                'success' => true,
                'data' => $rooms,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching rooms: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch rooms',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get room details.
     */
    public function getRoomById(int $id): array
    {
        try {
            $room = $this->roomRepository->findById($id, [
                'hotel',
                'seasonalPricing',
            ]);

            if (!$room) {
                return [
                    'success' => false,
                    'message' => 'Room not found',
                ];
            }

            return [
                'success' => true,
                'data' => $room,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching room: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch room',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get room by slug.
     */
    public function getRoomBySlug(int $hotelId, string $slug): array
    {
        try {
            $room = $this->roomRepository->findBySlug($hotelId, $slug, [
                'hotel',
                'seasonalPricing',
            ]);

            if (!$room) {
                return [
                    'success' => false,
                    'message' => 'Room not found',
                ];
            }

            return [
                'success' => true,
                'data' => $room,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching room: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch room',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check room availability.
     */
    public function checkAvailability(int $roomId, array $data): array
    {
        try {
            $result = $this->roomRepository->checkAvailability(
                $roomId,
                $data['check_in'],
                $data['check_out'],
                $data['rooms'] ?? 1
            );

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Error checking room availability: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to check availability',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new room.
     */
    public function createRoom(array $data): array
    {
        try {
            DB::beginTransaction();

            $room = $this->roomRepository->create($data);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Room created successfully',
                'data' => $room,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating room: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to create room',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update room.
     */
    public function updateRoom(int $id, array $data): array
    {
        try {
            DB::beginTransaction();

            $updated = $this->roomRepository->update($id, $data);

            if (!$updated) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Room not found or update failed',
                ];
            }

            DB::commit();

            $room = $this->roomRepository->findById($id);

            return [
                'success' => true,
                'message' => 'Room updated successfully',
                'data' => $room,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating room: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to update room',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete room.
     */
    public function deleteRoom(int $id): array
    {
        try {
            $deleted = $this->roomRepository->delete($id);

            if (!$deleted) {
                return [
                    'success' => false,
                    'message' => 'Room not found or delete failed',
                ];
            }

            return [
                'success' => true,
                'message' => 'Room deleted successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting room: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to delete room',
                'error' => $e->getMessage(),
            ];
        }
    }
}