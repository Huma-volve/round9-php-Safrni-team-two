<?php

namespace App\Helpers;

class ApiErrorCode
{
    public const VALIDATION = 'VALIDATION_ERROR';
    public const UNAUTHORIZED = 'UNAUTHORIZED';
    public const FORBIDDEN = 'FORBIDDEN';
    public const NOT_FOUND = 'NOT_FOUND';
    public const OTP_INVALID = 'OTP_INVALID';
    public const OTP_EXPIRED = 'OTP_EXPIRED';
    public const OTP_USED = 'OTP_USED';
    public const USER_NOT_VERIFIED = 'USER_NOT_VERIFIED';
    public const GOOGLE_EXCHANGE_FAILED = 'GOOGLE_EXCHANGE_FAILED';
    public const CONFLICT = 'CONFLICT';
}
