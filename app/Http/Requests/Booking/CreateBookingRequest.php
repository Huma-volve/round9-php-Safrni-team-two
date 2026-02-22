<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_id'          => 'required|integer|exists:rooms,id',
            'check_in'         => 'required|date|after_or_equal:today',
            'check_out'        => 'required|date|after:check_in',
            'adults'           => 'required|integer|min:1|max:10',
            'children'         => 'nullable|integer|min:0|max:10',
            'infants'          => 'nullable|integer|min:0|max:5',
            'rooms_count'      => 'nullable|integer|min:1|max:10',
            'currency'         => 'nullable|string|size:3',
            'special_requests' => 'nullable|string|max:1000',
            'guest_info'       => 'nullable|array',
            'guest_info.name'  => 'nullable|string|max:100',
            'guest_info.phone' => 'nullable|string|max:20',
            'guest_info.email' => 'nullable|email',
            'extras'           => 'nullable|array',
            'extras.*.id'      => 'required|integer',
            'extras.*.qty'     => 'required|integer|min:1',
        ];
    }
}