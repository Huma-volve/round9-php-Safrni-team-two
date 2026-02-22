<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\StoreHotelRequest;
use App\Http\Requests\Hotel\UpdateHotelRequest;
use App\Http\Resources\HotelDetailResource;
use App\Services\HotelService;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    public function __construct(
        protected HotelService $hotelService
    ) {}

    /**
     * إضافة فندق جديد
     */
    public function store(StoreHotelRequest $request): JsonResponse
    {
        $result = $this->hotelService->createHotel($request->validated());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => new HotelDetailResource($result['data']),
        ], 201);
    }

    /**
     * تعديل فندق
     */
    public function update(UpdateHotelRequest $request, int $id): JsonResponse
    {
        $result = $this->hotelService->updateHotel($id, $request->validated());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => new HotelDetailResource($result['data']),
        ]);
    }

    /**
     * حذف فندق
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->hotelService->deleteHotel($id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}