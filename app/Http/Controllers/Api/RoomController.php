<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\CheckRoomAvailabilityRequest;
use App\Http\Requests\Room\GetRoomsRequest;
use App\Http\Resources\RoomResource;
use App\Http\Resources\RoomDetailResource;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;

/**
 * Room Controller
 * 
 * Handles HTTP requests for rooms.
 */
class RoomController extends Controller
{
    public function __construct(
        protected RoomService $roomService
    ) {}

    /**
     * Get rooms for a hotel.
     */
    public function index(GetRoomsRequest $request, int $hotelId): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->input('per_page', 15);

        $result = $this->roomService->getRoomsForHotel($hotelId, $filters, $perPage);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => RoomResource::collection($result['data']),
            'meta' => [
                'current_page' => $result['data']->currentPage(),
                'total' => $result['data']->total(),
                'per_page' => $result['data']->perPage(),
                'last_page' => $result['data']->lastPage(),
            ],
        ]);
    }

    /**
     * Display a room.
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->roomService->getRoomById($id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new RoomDetailResource($result['data']),
        ]);
    }

    /**
     * Display room by slug.
     */
    public function showBySlug(int $hotelId, string $slug): JsonResponse
    {
        $result = $this->roomService->getRoomBySlug($hotelId, $slug);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new RoomDetailResource($result['data']),
        ]);
    }

    /**
     * Check room availability.
     */
    public function checkAvailability(CheckRoomAvailabilityRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->roomService->checkAvailability($id, $validated);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        $data = $result['data'];

        if (!$data['available']) {
            return response()->json([
                'success' => false,
                'message' => $data['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'available' => true,
                'room' => new RoomResource($data['room']),
                'pricing' => $data['pricing'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
            ],
        ]);
    }
}
