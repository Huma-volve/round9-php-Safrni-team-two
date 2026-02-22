<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Room\StoreRoomRequest;
use App\Http\Requests\Admin\Room\UpdateRoomRequest;
use App\Http\Resources\RoomDetailResource;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    public function __construct(
        protected RoomService $roomService
    ) {}

    // إضافة غرفة
    public function store(StoreRoomRequest $request, int $hotelId): JsonResponse
    {
        $data             = $request->validated();
        $data['hotel_id'] = $hotelId;

        $result = $this->roomService->createRoom($data);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => new RoomDetailResource($result['data']),
        ], 201);
    }

    // تعديل غرفة
    public function update(UpdateRoomRequest $request, int $id): JsonResponse
    {
        $result = $this->roomService->updateRoom($id, $request->validated());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => new RoomDetailResource($result['data']),
        ]);
    }

    // حذف غرفة
    public function destroy(int $id): JsonResponse
    {
        $result = $this->roomService->deleteRoom($id);

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