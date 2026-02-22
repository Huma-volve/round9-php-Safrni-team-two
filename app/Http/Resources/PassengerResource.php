<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassengerResource extends JsonResource
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
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name, // Should be accessor in Model
            'nationality' => $this->nationality,
            'passport_masked' => substr($this->passport_number, 0, 2) . '*****' . substr($this->passport_number, -2), // Privacy masking
            'ticket' => [
                'ticket_number' => $this->ticket->ticket_number ?? null,
                'seat' => $this->ticket->seat->seat_number ?? 'Not assigned', // e.g. "1A"
                'class' => $this->ticket->class_type ?? null,
                'price' => (float)($this->ticket->price_paid ?? 0),
            ],
            'special_requests' => $this->special_requests,
        ];
    }
}
