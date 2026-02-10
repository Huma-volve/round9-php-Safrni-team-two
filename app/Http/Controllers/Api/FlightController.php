<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FlightSearchRequest;
use App\Services\FlightService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    use ApiResponseTrait;

    protected $flightService;

    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    /**
     * Search for flights based on basic criteria and filters.
     * 
     * @param  \App\Http\Requests\Api\FlightSearchRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(FlightSearchRequest $request)
    {
        // Validation is automatically handled by FlightSearchRequest
        // If validation fails, it throws an exception and returns 422 automatically.

        $flights = $this->flightService->searchFlights($request->validated());

        return $this->successResponse($flights, 'Flights retrieved successfully');
    }

    /**
     * Display a specific flight with details.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $flight = $this->flightService->getFlightDetails($id);
            return $this->successResponse($flight, 'Flight details retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Flight not found', 404);
        }
    }

    /**
     * Compare multiple flights side-by-side.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function compare(Request $request)
    {
        $request->validate([
            'flight_ids' => 'required|array|min:2',
            'flight_ids.*' => 'exists:flights,id'
        ]);

        $comparison = $this->flightService->compareFlights($request->flight_ids);

        return $this->successResponse($comparison, 'Flights comparison retrieved successfully');
    }
}
