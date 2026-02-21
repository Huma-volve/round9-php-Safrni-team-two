<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'booking_reference' => $this->booking_reference,
            'hotel' => [
    'id'          => $this->hotel->id,
    'name'        => $this->hotel->name,
    'city'        => $this->hotel->city,
    'star_rating' => $this->hotel->star_rating,
    'main_image'  => $this->hotel->main_image_url,
],
'room' => [
    'id'                   => $this->room->id,
    'name'                 => $this->room->name,
    'bed_type'             => $this->room->bed_type,
    'max_adults'           => $this->room->max_adults,
    'base_price_per_night' => (float) $this->room->base_price_per_night,
    'is_refundable'        => $this->room->is_refundable,
],
            'check_in'          => $this->check_in->format('Y-m-d'),
            'check_out'         => $this->check_out->format('Y-m-d'),
            'nights'            => $this->nights,
            'guests'            => [
                'adults'   => $this->adults,
                'children' => $this->children,
                'infants'  => $this->infants,
            ],
            'rooms_count'       => $this->rooms_count,
            'pricing'           => [
                'price_per_night' => $this->price_per_night,
                'subtotal'        => $this->subtotal,
                'tax_amount'      => $this->tax_amount,
                'service_fee'     => $this->service_fee,
                'total_amount'    => $this->total_amount,
                'currency'        => $this->currency,
            ],
            'status'            => $this->status,
            'payment_status'    => $this->payment_status,
            'guest_info'        => $this->guest_info,
            'extras'            => $this->extras,
            'special_requests'  => $this->special_requests,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at'      => $this->cancelled_at?->toDateTimeString(),
            'can_cancel'        => $this->isCancellable(),
            'can_review'        => $this->isCompleted(),
            'created_at'        => $this->created_at->toDateTimeString(),
        ];
    }
}