<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use ApiResponseTrait;

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Create a new booking.
     * 
     * @param  BookingRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BookingRequest $request)
    {
        try {
            // Pass authenticated user ID if logged in
            $user = Auth::user();
            $booking = $this->bookingService->createBooking($request->validated(), $user);

            return $this->successResponse(new BookingResource($booking), 'Booking created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
