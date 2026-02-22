<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Search Hotel Request
 * 
 * Validates hotel search and filter parameters.
 */
class SearchHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
            'check_in' => 'nullable|date|after_or_equal:today',
            'check_out' => 'nullable|date|after:check_in',
            'adults' => 'nullable|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:20',
            'rooms' => 'nullable|integer|min:1|max:10',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'stars' => 'nullable|array',
            'stars.*' => 'integer|between:1,5',
            'min_rating' => 'nullable|numeric|between:0,5',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'recommended' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'sort_by' => 'nullable|in:name,price,rating',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'check_in.after_or_equal' => 'Check-in date must be today or later.',
            'check_out.after' => 'Check-out date must be after check-in date.',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price.',
        ];
    }
}
