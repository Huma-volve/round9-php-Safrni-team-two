<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
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
        ];
    }
}
