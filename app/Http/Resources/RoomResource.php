<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Room Resource
 * 
 * Transforms Room model to API response.
 */
class RoomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            
            // Images
            'photos' => $this->photos_urls,
            
            // Occupancy
            'occupancy' => [
                'max_adults' => $this->max_adults,
                'max_children' => $this->max_children,
                'max_infants' => $this->max_infants,
                'total' => $this->total_occupancy,
            ],
            
            // Room Details
            'details' => [
                'bed_type' => $this->bed_type,
                'number_of_beds' => $this->number_of_beds,
                'area' => [
                    'size' => (float) $this->room_area,
                    'unit' => $this->room_area_unit,
                ],
            ],
            
            // Pricing
            'pricing' => [
                'base_price_per_night' => (float) $this->base_price_per_night,
                'currency' => $this->currency,
                'total_for_range' => $this->when(
                    isset($this->total_price_for_range),
                    (float) $this->total_price_for_range
                ),
            ],
            
            // Availability
            'availability' => [
                'total_rooms' => $this->total_rooms,
                'is_refundable' => $this->is_refundable,
            ],
            
            // Amenities & Extras
            'amenities' => $this->amenities ?? [],
            'extras' => $this->extras ?? [],
            
            // Hotel
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}