<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Check Room Availability Request
 */
class CheckRoomAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'rooms' => 'nullable|integer|min:1|max:10',
            'adults' => 'nullable|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:20',
            'infants' => 'nullable|integer|min:0|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'check_in.required' => 'Check-in date is required.',
            'check_in.after_or_equal' => 'Check-in date must be today or later.',
            'check_out.required' => 'Check-out date is required.',
            'check_out.after' => 'Check-out date must be after check-in date.',
        ];
    }
}
