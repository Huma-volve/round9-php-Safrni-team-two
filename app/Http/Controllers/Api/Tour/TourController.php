<?php

namespace App\Http\Controllers\Api\Tour;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::query()
            ->withMin('tourPriceTier', 'adult_price')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $tours
        ]);
    }
}
