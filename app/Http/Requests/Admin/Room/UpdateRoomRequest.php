<?php

namespace App\Http\Requests\Admin\Room;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                 => 'sometimes|string|max:255',
            'description'          => 'sometimes|nullable|string',
            'max_adults'           => 'sometimes|integer|min:1',
            'max_children'         => 'sometimes|integer|min:0',
            'max_infants'          => 'sometimes|integer|min:0',
            'total_occupancy'      => 'sometimes|integer|min:1',
            'bed_type'             => 'sometimes|string|max:100',
            'number_of_beds'       => 'sometimes|integer|min:1',
            'room_area'            => 'sometimes|nullable|numeric|min:0',
            'room_area_unit'       => 'sometimes|nullable|string|in:sqm,sqft',
            'base_price_per_night' => 'sometimes|numeric|min:0',
            'currency'             => 'sometimes|nullable|string|size:3',
            'total_rooms'          => 'sometimes|integer|min:1',
            'is_refundable'        => 'sometimes|boolean',
            'amenities'            => 'sometimes|nullable|array',
            'amenities.*'          => 'string',
            'extras'               => 'sometimes|nullable|array',
            'is_active'            => 'sometimes|boolean',
            'display_order'        => 'sometimes|nullable|integer|min:0',
        ];
    }
}