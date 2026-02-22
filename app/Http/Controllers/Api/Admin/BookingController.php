<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Booking\UpdateBookingStatusRequest;
use App\Http\Requests\Booking\CancelBookingRequest;
use App\Http\Resources\BookingDetailResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    // جلب كل الحجوزات
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'payment_status', 'hotel_id']);
        $perPage = $request->input('per_page', 15);

        $result = $this->bookingService->getAllBookings($filters, $perPage);

        return response()->json([
            'success' => true,
            'data'    => BookingDetailResource::collection($result['data']),
            'meta'    => [
                'current_page' => $result['data']->currentPage(),
                'total'        => $result['data']->total(),
                'per_page'     => $result['data']->perPage(),
                'last_page'    => $result['data']->lastPage(),
            ],
        ]);
    }

    // تفاصيل حجز
    public function show(int $id): JsonResponse
    {
        $result = $this->bookingService->getBookingById($id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new BookingDetailResource($result['data']),
        ]);
    }

    // تغيير status
    public function updateStatus(UpdateBookingStatusRequest $request, int $id): JsonResponse
    {
        $result = $this->bookingService->updateBookingStatus($id, $request->status);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => new BookingDetailResource($result['data']),
        ]);
    }

    // إلغاء حجز
    public function cancel(CancelBookingRequest $request, int $id): JsonResponse
    {
        $result = $this->bookingService->cancelBooking($id, $request->reason);

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