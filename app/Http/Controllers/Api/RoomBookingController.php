<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Requests\Booking\CancelBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingDetailResource;
use App\Services\RoomBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomBookingController extends Controller
{
    public function __construct(
        protected RoomBookingService $bookingService
    ) {}

    /**
     * GET /api/safarni/bookings/rooms
     * قائمة حجوزات المستخدم
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status']);
        $perPage = $request->input('per_page', 15);

        $result = $this->bookingService->getUserBookings(
            auth()->id(),
            $filters,
            $perPage
        );

        return response()->json([
            'success' => true,
            'data'    => BookingResource::collection($result['data']),
            'meta'    => [
                'current_page' => $result['data']->currentPage(),
                'total'        => $result['data']->total(),
                'per_page'     => $result['data']->perPage(),
                'last_page'    => $result['data']->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/safarni/bookings/rooms
     * إنشاء حجز جديد
     */
    public function store(CreateBookingRequest $request): JsonResponse
    {
        $result = $this->bookingService->createBooking(
            auth()->id(),
            $request->validated()
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'data'    => new BookingDetailResource($result['data']),
        ], 201);
    }

    /**
     * GET /api/safarni/bookings/rooms/{id}
     * تفاصيل حجز محدد
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->bookingService->getBooking($id, auth()->id());

        if (! $result['success']) {
            $code = $result['message'] === 'Unauthorized.' ? 403 : 404;
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $code);
        }

        return response()->json([
            'success' => true,
            'data'    => new BookingDetailResource($result['data']),
        ]);
    }

    /**
     * POST /api/safarni/bookings/rooms/{id}/cancel
     * إلغاء حجز
     */
    public function cancel(CancelBookingRequest $request, int $id): JsonResponse
    {
        $result = $this->bookingService->cancelBooking(
            $id,
            auth()->id(),
            $request->validated('reason', '')
        );

        if (! $result['success']) {
            $code = $result['message'] === 'Unauthorized.' ? 403 : 422;
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $code);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}