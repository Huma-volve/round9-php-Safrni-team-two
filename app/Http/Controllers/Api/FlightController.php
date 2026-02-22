<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FlightSearchRequest;
use App\Http\Resources\FlightResource;
use App\Http\Resources\SeatResource;
use App\Services\FlightService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        // Use FlightResource to format the output
        $flights = $this->flightService->searchFlights($request->validated());

        return $this->successResponse(FlightResource::collection($flights), 'Flights retrieved successfully');
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
            return $this->successResponse(new FlightResource($flight), 'Flight details retrieved successfully');
        } catch (ModelNotFoundException $e) {
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

        return $this->successResponse(FlightResource::collection($comparison), 'Flights comparison retrieved successfully');
    }

    /**
     * Get seat map for a flight.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSeats($id)
    {
        try {
            $seats = $this->flightService->getFlightSeats($id);
            return $this->successResponse(SeatResource::collection($seats), 'Seat map retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Flight not found', 404);
        }
    }
}
