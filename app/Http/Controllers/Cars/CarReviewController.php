<?php

namespace App\Http\Controllers\Cars;

use App\Http\Controllers\Controller;
use App\Models\CarBooking;
use App\Models\CarReview;
use Illuminate\Http\Request;

class CarReviewController extends Controller
{
    public function index($carId)
    {
        $reviews = CarReview::with('user')
            ->where('car_id', $carId)
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // نتأكد إن الحجز بتاعه
        $booking = CarBooking::where('id', $request->car_booking_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Unauthorized booking'
            ], 403);
        }

        $exists = CarReview::where('car_booking_id', $request->car_booking_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You already reviewed this booking'
            ], 400);
        }

        $review = CarReview::create([
            'car_id' => $request->car_id,
            'user_id' => $user->id,
            'car_booking_id' => $request->car_booking_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review created successfully',
            'data' => $review
        ], 201);
    }
}
