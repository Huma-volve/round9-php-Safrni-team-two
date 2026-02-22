<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'flight_number' => $this->flight_number,
            'carrier' => $this->carrier,
            'origin' => new AirportResource($this->whenLoaded('origin')),
            'destination' => new AirportResource($this->whenLoaded('destination')),
            'schedule' => [
                'departure_time' => $this->departure_time->format('Y-m-d H:i:s'),
                'arrival_time' => $this->arrival_time->format('Y-m-d H:i:s'),
                'duration_minutes' => $this->departure_time->diffInMinutes($this->arrival_time),
                'duration_formatted' => $this->departure_time->diff($this->arrival_time)->format('%Hh %Im'),
            ],
            'status' => $this->status,
            'refundability' => (bool)$this->refundability,
            'aircraft' => $this->whenLoaded('aircraft', function () {
                return [
                    'model' => $this->aircraft->model,
                    'manufacturer' => $this->aircraft->manufacturer,
                ];
            }),
            'fares' => FlightFareResource::collection($this->whenLoaded('fares')), // Detailed pricing
            'starting_price' => $this->whenLoaded('fares', function () {
               return (float)$this->fares->min('base_price'); // Quick view for "Starts from $X"
            }),
        ];
    }
}
