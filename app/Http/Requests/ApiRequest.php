<?php

namespace App\Http\Requests;

use App\Helpers\ApiErrorCode;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiRequest extends FormRequest
{
    use ApiResponse;
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->error(
                ApiErrorCode::VALIDATION,
                'Validation failed.',
                $validator->errors(),
                422
            )
        );
    }
}
