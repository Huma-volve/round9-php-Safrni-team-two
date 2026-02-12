<?php

namespace App\Http\Controllers\Api\Tour;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tour\CheckAvailableRequest;
use App\Http\Requests\Api\Tour\TourBookingRequest;
use App\Models\Booking;
use App\Models\Tour;
use App\Services\TourService\TourBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourBookingController extends Controller
{
    private $tourBookingService;

    public function __construct(TourBookingService $tourBookingService)
    {
        $this->tourBookingService = $tourBookingService;
    }
    public function checkAvailability(CheckAvailableRequest $request, $id)
    {
        $tour  = Tour::find($id);
        if (! $tour) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Tour not found',
                    'details' => [
                        'tour_id' => [$id],
                    ],
                ],
            ], 404);
        }
        return $this->tourBookingService->checkAvailable($request, $tour);
    }



    public function booking(TourBookingRequest $request, int $id): JsonResponse
    {
        $tour = Tour::find($id);
        if (!$tour) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Tour not found',
                    'details' => ['tour_id' => [$id]],
                ],
            ], 404);
        }

        try {
            $booking = $this->tourBookingService->createTourBooking($request, $tour);

            return response()->json([
                'success' => true,
                'data' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                    'total_price' => $booking->total_price,
                    'message' => 'Booking created. Proceed to payment.',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'details' => [],
                ],
            ], 409);
        }
    }

    public function show($id)
    {
        $booking = Booking::where('category', 'tour')
            ->where('item_id', $id)
            ->where('user_id', auth()->id()) //auth()->id()
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }
}
