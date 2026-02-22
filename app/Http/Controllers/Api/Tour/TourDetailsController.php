<?php

namespace App\Http\Controllers\Api\Tour;

use App\Http\Controllers\Controller;
use App\Services\TourService\TourDetailsService;
use Illuminate\Http\Request;

class TourDetailsController extends Controller
{
    private $tourDetailsService;
    public function __construct(TourDetailsService $tourDetailsService)
    {
        $this->tourDetailsService = $tourDetailsService;
    }

    public function show(int $id)
    {
        $data = $this->tourDetailsService->getTourDetails($id);

        if (!$data || $data['tour'] == "Not Found") {
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

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
