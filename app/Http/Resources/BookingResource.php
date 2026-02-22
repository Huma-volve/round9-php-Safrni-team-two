<?php

namespace App\Http\Resources;

<<<<<<< HEAD
use App\Http\Resources\FlightResource;
use App\Http\Resources\PassengerResource;
use Illuminate\Http\Request;
=======
>>>>>>> 0f192b0e788d514cd46aa7deb40e569a60a4a995
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
<<<<<<< HEAD
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
=======
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'booking_reference' => $this->booking_reference,
            'hotel'             => [
                'id'         => $this->hotel->id,
                'name'       => $this->hotel->name,
                'main_image' => $this->hotel->main_image_url,
                'city'       => $this->hotel->city,
            ],
            'room'              => [
                'id'   => $this->room->id,
                'name' => $this->room->name,
            ],
            'check_in'          => $this->check_in->format('Y-m-d'),
            'check_out'         => $this->check_out->format('Y-m-d'),
            'nights'            => $this->nights,
            'total_amount'      => $this->total_amount,
            'currency'          => $this->currency,
            'status'            => $this->status,
            'payment_status'    => $this->payment_status,
            'created_at'        => $this->created_at->toDateTimeString(),
>>>>>>> 0f192b0e788d514cd46aa7deb40e569a60a4a995
        ];
    }
}
