<?php

namespace App\Services;

use App\Models\Flight;
use Illuminate\Database\Eloquent\Builder;

class FlightService
{
    /**
     * Search flights based on criteria and filters.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchFlights(array $data)
    {
        $query = Flight::query()
            ->with(['origin', 'destination', 'aircraft', 'fares'])
            ->whereHas('origin', function ($q) use ($data) {
                $q->where('airport_code', $data['origin_code']);
            })
            ->whereHas('destination', function ($q) use ($data) {
                $q->where('airport_code', $data['destination_code']);
            })
            ->whereDate('departure_time', $data['date'])
            ->where('status', 'scheduled');

        // Apply filters
        $this->applyFilters($query, $data);

        return $query->get();
    }

    /**
     * Apply optional filters to the flight query.
     *
     * @param Builder $query
     * @param array $data
     */
    protected function applyFilters(Builder $query, array $data)
    {
        // Filter by Carriers
        if (isset($data['carriers'])) {
            $carriers = explode(',', $data['carriers']);
            $query->whereIn('carrier', $carriers);
        }

        // Filter by Departure Time Range
        if (isset($data['min_departure_time']) && isset($data['max_departure_time'])) {
            $query->whereTime('departure_time', '>=', $data['min_departure_time'])
                  ->whereTime('departure_time', '<=', $data['max_departure_time']);
        }

        // Filter by Fares related criteria (Price, Class, Stops, Available Seats)
        $passengerCount = $data['passengers'] ?? 1;

        if (isset($data['class_type']) || isset($data['min_price']) || isset($data['max_price']) || isset($data['stops']) || $passengerCount > 0) {
            $query->whereHas('fares', function ($q) use ($data, $passengerCount) {
                // Ensure enough seats are available for the requested passengers
                $q->where('seats_available', '>=', $passengerCount);

                if (isset($data['class_type'])) {
                    $q->where('class_type', $data['class_type']);
                }
                if (isset($data['min_price'])) {
                    $q->where('base_price', '>=', $data['min_price']);
                }
                if (isset($data['max_price'])) {
                    $q->where('base_price', '<=', $data['max_price']);
                }
                if (isset($data['stops'])) {
                    $stops = explode(',', $data['stops']);
                    $q->whereIn('stops', $stops);
                }
            });
        }
    }

    /**
     * Get flight details by ID.
     *
     * @param int $id
     * @return Flight
     */
    public function getFlightDetails($id)
    {
        return Flight::with(['origin', 'destination', 'aircraft', 'fares', 'reviews.user'])
            ->findOrFail($id);
    }

    /**
     * Compare multiple flights.
     *
     * @param array $flightIds
     * @return \Illuminate\Support\Collection
     */
    public function compareFlights(array $flightIds)
    {
        $flights = Flight::with(['origin', 'destination', 'aircraft', 'fares'])
            ->whereIn('id', $flightIds)
            ->get();

        return $flights->map(function ($flight) {
            $cheapestFare = $flight->fares->sortBy('base_price')->first();
            return [
                'id' => $flight->id,
                'flight_number' => $flight->flight_number,
                'carrier' => $flight->carrier,
                'origin' => $flight->origin->city,
                'destination' => $flight->destination->city,
                'departure_time' => $flight->departure_time,
                'arrival_time' => $flight->arrival_time,
                'duration' => $flight->arrival_time->diffInMinutes($flight->departure_time),
                'stops' => $cheapestFare ? $cheapestFare->stops : 0,
                'price' => $cheapestFare ? $cheapestFare->base_price : null,
                'baggage' => $cheapestFare ? $cheapestFare->baggage_price : 0,
                'refundability' => $flight->refundability,
                'aircraft' => $flight->aircraft->model,
            ];
        });
    }
}
