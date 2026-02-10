<?php

namespace App\Services\TourService;

use App\Models\Category;
use App\Models\Review;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourPriceTier;
use App\Models\TourSchedule;
use Carbon\Carbon;

class TourDetailsService
{
    public function getTour(int $tourId)
    {
        $tour = Tour::find($tourId); 
        if (!$tour) {
            return "Not Found";
        }
        return $tour;
    }

    public function getSchedules(int $tourId): array
    {
        $schedule = TourSchedule::where('tour_id', $tourId)->first();
        if (!$schedule) {
            return [];
        }
        $startDate = Carbon::parse($schedule->start_date)->startOfDay();
        $endDate   = Carbon::parse($schedule->end_date)->startOfDay();

        $periodDays = $startDate->diffInDays($endDate) + 1;

        return [
            $schedule,
            'period_days' => $periodDays,
        ];
    }

    public function getPriceTiers(int $tourId)
    {
        return TourPriceTier::where('tour_id', $tourId)->get();
    }

    public function getActivities(int $tourId)
    {
        return TourActivity::where('tour_id', $tourId)->get();
    }
    public function getReviews(int $tourId)
    {
        $category_id = Category::where('key', "tour")->value('id');
        $reviews = Review::where('category_id', $category_id)->where('item_id', $tourId)->get();
        return $reviews;
    }
    public function getTourDetails(int $tourId): array
    {
        return [
            'tour'       => $this->getTour($tourId),
            'schedule'   => $this->getSchedules($tourId),
            'price_tiers' => $this->getPriceTiers($tourId),
            'activities' => $this->getActivities($tourId),
            'reviews' => $this->getReviews($tourId),
        ];
    }
}
