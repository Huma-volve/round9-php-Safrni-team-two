<?php

namespace App\Http\Requests\Favorite;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToggleFavoriteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'      => ['required', 'string', Rule::in(['hotel', 'room', 'tour', 'car', 'flight'])],
            'entity_id' => 'required|integer|min:1',
        ];
    }
}
