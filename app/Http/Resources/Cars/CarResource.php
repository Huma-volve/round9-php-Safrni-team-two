<?php

namespace App\Http\Resources\Cars;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'name' => $this->model,
            'brand' => $this->brand->name,
            'transmission' => $this->transmission,
            'fuel_type' => $this->fuel_type,
            'seats' => $this->seats_count,
            'image' => $this->images[1],
            'price_per_hour'=> $this->base_price_per_hour,
            'location' => $this->location,
        ];
    }
}
