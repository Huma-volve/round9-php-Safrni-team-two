<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            'flight_id' => 'required|exists:flights,id',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',

            // Passengers Details
            'passengers' => 'required|array|min:1',
            'passengers.*.title' => 'nullable|string|in:Mr,Mrs,Ms,Dr,Eng',
            'passengers.*.first_name' => 'required|string|max:50',
            'passengers.*.last_name' => 'required|string|max:50',
            'passengers.*.date_of_birth' => 'required|date|before:today',
            'passengers.*.passport_number' => 'required|string|max:20',
            'passengers.*.nationality' => 'required|string|max:50',
            'passengers.*.special_requests' => 'nullable|string|max:500',

            // Required: Selected Seat ID for each passenger
            'passengers.*.seat_id' => 'required|exists:seats,id',
            // Default class if not seat selected
            'class_type' => 'required|in:economy,business,first',
        ];
    }
}
