<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'       => ['required', 'string', Rule::in(['hotel', 'room', 'tour', 'car'])],
            'entity_id'  => 'required|integer|min:1',
            'booking_id' => 'nullable|integer|exists:room_bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'title'      => 'nullable|string|max:150',
            'body'       => 'required|string|min:10|max:2000',
            'photos'     => 'nullable|array|max:5',
            'photos.*'   => 'string',
        ];
    }
}