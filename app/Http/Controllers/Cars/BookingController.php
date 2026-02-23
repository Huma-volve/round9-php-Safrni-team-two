<?php

namespace App\Http\Controllers\Cars;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\Cars\BookingResource;
use App\Models\Car;
use App\Models\CarBooking;
use App\Traits\CalculateCarPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    use CalculateCarPrice;
    public function calculatePricing(Car $car, Request $request)
    {
        $hours = (int) ($request->hours ?? 1);
        $tiers = $car->pricingTiers()->orderBy('min_hours')->get();

        if ($tiers->isEmpty()) {
            $total = $car->base_price_per_hour * $hours;
            return response()->json([
                'total' => $total,
                'breakdown' => [['hours' => $hours, 'rate' => $car->base_price_per_hour]]
            ]);
        }

        $total = 0;
        $remaining = $hours;

        foreach ($tiers as $tier) {
            if ($remaining <= 0) break;
            $hoursInTier = min($remaining, ($tier->max_hours ?? PHP_INT_MAX) - ($tier->min_hours - 1));
            $total += $hoursInTier * $tier->price_per_hour;
            $remaining -= $hoursInTier;
        }

        return response()->json([
            'total' => round($total, 2),
            'hours' => $hours,
            'breakdown' => $tiers->map(fn($t) => [
                'tier' => "{$t->min_hours}-" . ($t->max_hours ?? '∞'),
                'rate' => $t->price_per_hour
            ])
        ]);
    }

    public function calculateTotal(BookingRequest $request)
    {
        $request->validated();

        $pickup = Carbon::parse($request->pickup_datetime);
        $dropoff = Carbon::parse($request->dropoff_datetime);

        if ($dropoff->lte($pickup)) {
            return response()->json(['error' => 'Dropoff must be after pickup'], 400);
        }

        $hours = $dropoff->diffInHours($pickup, true);

        $carPrice = $this->calculateCarPrice($request->car_id, $hours);
        $total = $carPrice;

        return response()->json([
            'total' => round($total, 2),
            'details' => [
                'car_price' => round($carPrice, 2),
                'hours' => $hours,
                'days' => ceil($hours / 24),
                'pickup' => $pickup->format('Y-m-d H:i'),
                'dropoff' => $dropoff->format('Y-m-d H:i')
            ]
        ]);
    }


    public function store(BookingRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $pickup = Carbon::parse($request->pickup_datetime);
            $dropoff = Carbon::parse($request->dropoff_datetime);
            $hours = $dropoff->diffInHours($pickup, true);

            $overlapping = CarBooking::where('car_id', $request->car_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($pickup, $dropoff) {
                    $query->whereBetween('pickup_datetime', [$pickup, $dropoff])
                        ->orWhereBetween('dropoff_datetime', [$pickup, $dropoff])
                        ->orWhere(function ($q) use ($pickup, $dropoff) {
                            $q->where('pickup_datetime', '<=', $pickup)
                                ->where('dropoff_datetime', '>=', $dropoff);
                        });
                })->exists();

            if ($overlapping) {
                throw ValidationException::withMessages([
                    'car_id' => 'Car is not available for selected dates'
                ]);
            }

            $totalPrice = $this->calculateCarPrice($request->car_id, $hours);

            $booking = CarBooking::create([
                'user_id' => auth()->id(),
                'car_id' => $request->car_id,
                'pickup_datetime' => $pickup,
                'dropoff_datetime' => $dropoff,
                'payable_type' => $request->payable_type,
                'pickup_location' => $request->pickup_location,
                'dropoff_location' => $request->dropoff_location,
                'driver_age' => $request->driver_age,
                'total_price' => $totalPrice,
                'status' => 'pending',
                
            ]);

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => $booking->load('car', 'user')
            ], 201);
        });
    }
}
