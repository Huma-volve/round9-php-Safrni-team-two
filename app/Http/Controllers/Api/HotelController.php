<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\SearchHotelRequest;
use App\Http\Requests\Hotel\CheckAvailabilityRequest;
use App\Http\Resources\HotelResource;
use App\Http\Resources\HotelDetailResource;
use App\Services\HotelService;
use Illuminate\Http\JsonResponse;

/**
 * Hotel Controller
 * 
 * Handles HTTP requests and responses for hotels.
 * Follows Single Responsibility Principle - only handles HTTP layer.
 * Business logic is delegated to HotelService.
 */
class HotelController extends Controller
{
    public function __construct(
        protected HotelService $hotelService
    ) {}

    /**
     * Display a listing of hotels.
     * 
     * @param SearchHotelRequest $request
     * @return JsonResponse
     */
    public function index(SearchHotelRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->input('per_page', 15);

        $result = $this->hotelService->getHotels($filters, $perPage);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => HotelResource::collection($result['data']),
            'meta' => [
                'current_page' => $result['data']->currentPage(),
                'total' => $result['data']->total(),
                'per_page' => $result['data']->perPage(),
                'last_page' => $result['data']->lastPage(),
            ],
        ]);
    }

    /**
     * Display the specified hotel by ID.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->hotelService->getHotelById($id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new HotelDetailResource($result['data']),
        ]);
    }

    /**
     * Display the specified hotel by slug.
     * 
     * @param string $slug
     * @return JsonResponse
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $result = $this->hotelService->getHotelBySlug($slug);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new HotelDetailResource($result['data']),
        ]);
    }

    /**
     * Get recommended hotels.
     * 
     * @return JsonResponse
     */
    public function recommended(): JsonResponse
    {
        $result = $this->hotelService->getRecommendedHotels(10);

        return response()->json([
            'success' => true,
            'data' => HotelResource::collection($result['data']),
        ]);
    }

    /**
     * Get featured hotels.
     * 
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        $result = $this->hotelService->getFeaturedHotels(10);

        return response()->json([
            'success' => true,
            'data' => HotelResource::collection($result['data']),
        ]);
    }

    /**
     * Get nearby hotels.
     * 
     * @param SearchHotelRequest $request
     * @return JsonResponse
     */
    public function nearby(SearchHotelRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->hotelService->getNearbyHotels(
            $validated['latitude'],
            $validated['longitude'],
            $validated['radius'] ?? 10
        );

        return response()->json([
            'success' => true,
            'data' => HotelResource::collection($result['data']),
        ]);
    }

    /**
     * Check hotel availability.
     * 
     * @param CheckAvailabilityRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function checkAvailability(CheckAvailabilityRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->hotelService->checkAvailability(
            $id,
            $validated['check_in'],
            $validated['check_out']
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'hotel' => new HotelResource($result['data']['hotel']),
                'available' => $result['data']['available'],
                'check_in' => $result['data']['check_in'],
                'check_out' => $result['data']['check_out'],
            ],
        ]);
    }


}