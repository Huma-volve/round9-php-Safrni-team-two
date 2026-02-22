<?php

namespace App\Http\Requests\Admin\Room;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'max_adults'           => 'required|integer|min:1',
            'max_children'         => 'nullable|integer|min:0',
            'max_infants'          => 'nullable|integer|min:0',
            'total_occupancy'      => 'required|integer|min:1',
            'bed_type'             => 'required|string|max:100',
            'number_of_beds'       => 'required|integer|min:1',
            'room_area'            => 'nullable|numeric|min:0',
            'room_area_unit'       => 'nullable|string|in:sqm,sqft',
            'base_price_per_night' => 'required|numeric|min:0',
            'currency'             => 'nullable|string|size:3',
            'total_rooms'          => 'required|integer|min:1',
            'is_refundable'        => 'boolean',
            'amenities'            => 'nullable|array',
            'amenities.*'          => 'string',
            'extras'               => 'nullable|array',
            'is_active'            => 'boolean',
            'display_order'        => 'nullable|integer|min:0',
        ];
    }
}