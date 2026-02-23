<?php

namespace App\Traits;

use App\Models\Car;
use Carbon\Carbon;

trait CalculateCarPrice
{
    public function calculateCarPrice($carId, $hours)
    {
        // ✅ منع القيم السالبة
        $hours = max(1, abs((int) $hours));

        $car = Car::findOrFail($carId);
        $tiers = $car->pricingTiers()->orderBy('min_hours')->get(); // ✅ tires مش pricingTiers

        if ($tiers->isEmpty()) {
            return $car->base_price_per_hour * $hours;
        }

        $total = 0;
        $remaining = $hours;

        foreach ($tiers as $tier) {
            if ($remaining <= 0) break;

            $hoursInTier = min($remaining, ($tier->max_hours ?? PHP_INT_MAX) - ($tier->min_hours - 1));
            $total += $hoursInTier * $tier->price_per_hour;
            $remaining -= $hoursInTier;
        }

        return round($total, 2);
    }
}
