<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FlightSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Search Criteria (Required)
            'origin_code' => 'required|string|exists:airports,airport_code|different:destination_code',
            'destination_code' => 'required|string|exists:airports,airport_code',
            'date' => 'required|date|after_or_equal:today',
            'passengers' => 'sometimes|integer|min:1|max:9',

            // Filters (Optional)
            'class_type' => 'nullable|in:economy,business,first',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric' . ($this->filled('min_price') ? '|gte:min_price' : ''),
            'stops' => 'nullable|string', // comma-separated: 0,1,2+
            'carriers' => 'nullable|string', // comma-separated airline codes or names
            'min_departure_time' => 'nullable|date_format:H:i',
            'max_departure_time' => 'nullable|date_format:H:i|after:min_departure_time',
        ];
    }
}
