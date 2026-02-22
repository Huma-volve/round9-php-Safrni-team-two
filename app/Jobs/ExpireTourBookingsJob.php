<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\TourSchedule;
use Illuminate\Support\Facades\DB;

class ExpireTourBookingsJob
{
    public function handle(): void
    {
        $expiredBookings = Booking::where('category', 'tour')
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {

                $schedule = TourSchedule::where('tour_id', $booking->item_id)
                    ->lockForUpdate()
                    ->first();

                if (!$schedule) return;

                $details = $booking->details;

                if (!$details) return;

                $totalPeople = $details->meta['total_people'] ?? 0;

                $schedule->available_slots += $totalPeople;
                $schedule->save();

                $booking->update(['status' => 'expired']);
            });
        }
    }
}
