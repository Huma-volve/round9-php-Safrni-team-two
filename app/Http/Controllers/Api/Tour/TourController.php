<?php

namespace App\Http\Controllers\Api\Tour;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $validator = validator($request->all(), [
            'search' => 'nullable|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code'    => 'VALIDATION_ERROR',
                    'message' => 'Invalid request parameters',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        $tours = Tour::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->withMin('tourPriceTier', 'adult_price')
            ->withSum('schedules', 'available_slots')
            ->paginate(10);


        return response()->json([
            'success' => true,
            'data'    => $tours,
        ]);
    }
}
