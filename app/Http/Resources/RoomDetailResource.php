<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Room Detail Resource
 * 
 * Extended room information with full details.
 */
class RoomDetailResource extends JsonResource
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
            ],
            
            // Seasonal Pricing
            'seasonal_pricing' => $this->whenLoaded('seasonalPricing', function () {
                return $this->seasonalPricing->map(function ($pricing) {
                    return [
                        'id' => $pricing->id,
                        'name' => $pricing->name,
                        'start_date' => $pricing->start_date->toDateString(),
                        'end_date' => $pricing->end_date->toDateString(),
                        'price_per_night' => (float) $pricing->price_per_night,
                        'min_nights' => $pricing->min_nights,
                        'is_active' => $pricing->is_active,
                    ];
                });
            }),
            
            // Availability
            'availability' => [
                'total_rooms' => $this->total_rooms,
                'is_refundable' => $this->is_refundable,
            ],
            
            // Amenities & Extras
            'amenities' => $this->amenities ?? [],
            'extras' => $this->extras ?? [],
            
            // Display Order
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            
            // Hotel
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}