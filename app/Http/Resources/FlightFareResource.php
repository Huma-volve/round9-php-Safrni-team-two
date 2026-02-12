<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightFareResource extends JsonResource
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
            'class_type' => $this->class_type,
            'base_price' => (float)$this->base_price,
            'taxes' => (float)$this->taxes,
            'total_price' => (float)($this->base_price + $this->taxes + $this->baggage_price), // Calculate total
            'baggage_price' => (float)$this->baggage_price,
            'seats_available' => $this->seats_available,
            'stops' => $this->stops,
            'is_refundable' => (bool)$this->is_refundable,
        ];
    }
}
