<?php

namespace App\Http\Resources;

use App\Http\Resources\FlightResource;
use App\Http\Resources\PassengerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'reference' => $this->booking_reference,
            'status' => $this->status,
            'contact' => [
                'email' => $this->contact_email,
                'phone' => $this->contact_phone,
            ],
            'financials' => [
                'total_price' => (float)$this->total_price,
                'tax_amount' => (float)$this->tax_amount,
                'currency' => 'EGP', // Default for now
            ],
            'booking_date' => $this->created_at->format('Y-m-d H:i'),
            // Assuming simplified flight info from one of the tickets or the flight relation
            'flight_summary' => new FlightResource($this->flights->first()), 
            'passengers' => PassengerResource::collection($this->whenLoaded('passengers')),
        ];
    }
}
