<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
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
            'row_number' => $this->row_number,
            'column_letter' => $this->column_letter,
            'seat_number' => $this->seat_number, // Accessor from model (e.g., "1A")
            'class_type' => $this->class_type,
            'position' => $this->seat_position,
            // 'is_booked' will be passed explicitly when collecting resources, or we check a dynamic attribute
            'is_booked' => $this->is_booked ?? false, 
            'price_modifier' => 0, // Future: specific seat pricing (e.g. extra legroom)
        ];
    }
}
