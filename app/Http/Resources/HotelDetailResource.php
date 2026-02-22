<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Hotel Detail Resource
 * 
 * Extended hotel information with full details.
 */
class HotelDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'full_description' => $this->full_description,
            
            // Location
            'location' => [
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'coordinates' => [
                    'latitude' => $this->latitude ? (float) $this->latitude : null,
                    'longitude' => $this->longitude ? (float) $this->longitude : null,
                ],
            ],
            
            // Images
            'images' => [
                'main' => $this->main_image_url,
                'gallery' => $this->gallery_urls,
            ],
            
            // Rating
            'rating' => [
                'stars' => $this->star_rating,
                'overall' => (float) $this->overall_rating,
                'total_reviews' => $this->total_reviews,
            ],
            
            // Amenities
            'amenities' => $this->amenities ?? [],
            
            // Policies
            'policies' => [
                'check_in_time' => $this->check_in_time?->format('H:i'),
                'check_out_time' => $this->check_out_time?->format('H:i'),
                'cancellation' => $this->cancellation_policy,
                'additional' => $this->policies ?? [],
            ],
            
            // Contact
            'contact' => [
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'additional' => $this->contact_info ?? [],
            ],
            
            // Pricing
            'pricing' => [
                'average_price' => (float) $this->average_price,
                'currency' => 'USD',
                'tax_percentage' => (float) $this->tax_percentage,
                'service_fee' => (float) $this->service_fee,
            ],
            
            // Flags
            'is_recommended' => $this->is_recommended,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            
            // Rooms
            'rooms' => RoomResource::collection($this->whenLoaded('activeRooms')),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
