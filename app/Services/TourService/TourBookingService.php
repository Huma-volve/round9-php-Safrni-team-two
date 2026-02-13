<?php

namespace App\Services\TourService;

use App\Jobs\ExpireTourBookingsJob;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\TourPriceTier;
use App\Models\TourSchedule;
use App\Repositories\TourRepositery\TourBookingInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TourBookingService
{

    public function checkAvailable($request, $tour)
    {
        $tourSchedule = TourSchedule::where("tour_id", $tour->id)->first();

        if (!$tourSchedule) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Tour schedule not found',
                    'details' => ['tour_id' => [$tour->id]],
                ],
            ], 404);
        }

        $totalPeople = $request->adult + $request->child + $request->infant;

        if ($totalPeople > $tourSchedule->available_slots) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Not enough available slots',
                    'details' => [
                        'requested' => $totalPeople,
                        'available' => $tourSchedule->available_slots,
                        'tour_id' => $tour->id
                    ],
                ],
            ], 409);
        }

        $totalPrice = $this->totalprice($request, $tour);

        $startDate = Carbon::parse($tourSchedule->start_date)->startOfDay();
        $endDate   = Carbon::parse($tourSchedule->end_date)->startOfDay();

        $periodDays = $startDate->diffInDays($endDate) + 1;

        return response()->json([
            'success' => true,
            'data' => [
                'requested_slots' => $totalPeople,
                'available_slots' => $tourSchedule->available_slots,
                'total_price' => $totalPrice,
                'start_date' => $tourSchedule->start_date,
                'end_date' => $tourSchedule->end_date,
                'total_days' => $periodDays,
                'tour_id' => $tour->id
            ],
        ]);
    }


    public function totalprice($request, $tour)
    {

        $tourPriceTire = TourPriceTier::where("tour_id", $tour->id)->first();
        $total = $request->adult * $tourPriceTire->adult_price +
            $request->child * $tourPriceTire->child_price +
            $request->infant * $tourPriceTire->infant_price;
        return $total;
    }



    public function createTourBooking($request, $tour): Booking
    {
        $adult = (int) $request->adult;
        $child = (int) ($request->child ?? 0);
        $infant = (int) ($request->infant ?? 0);
        $totalPeople = $adult + $child + $infant;

        return DB::transaction(function () use ($tour, $totalPeople, $adult, $child, $infant) {

            $tourSchedule = TourSchedule::where('tour_id', $tour->id)
                ->lockForUpdate()
                ->first();

            if (!$tourSchedule) {
                throw new Exception('Tour schedule not found');
            }

            if ($tourSchedule->available_slots < $totalPeople) {
                throw new Exception('Not enough available slots');
            }

            $tourSchedule->available_slots -= $totalPeople;
            $tourSchedule->save();

            $priceTier = $tour->tourPriceTier()->first();
            if (!$priceTier) {
                throw new Exception('Price tier not found');
            }

            $totalPrice =
                $adult * $priceTier->adult_price +
                $child * $priceTier->child_price +
                $infant * $priceTier->infant_price;

            $booking = Booking::create([
                'user_id' => auth()->id(), 
                'category' => 'tour',
                'item_id' => $tour->id,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_price' => $totalPrice,
                'expires_at' => now()->addMinutes(4), // expired time
            ]);

            $booking->details()->create([
                'meta' => [
                    'adult' => $adult,
                    'child' => $child,
                    'infant' => $infant,
                    'total_people' => $totalPeople,
                    'total_price' => $totalPrice,
                ]
            ]);
            return $booking;
        });
    }
}
