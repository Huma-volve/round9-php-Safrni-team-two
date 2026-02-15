<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetNewPasswordRequest;
use App\Services\PasswordResetService;
use App\Traits\ApiResponse;

class PasswordController extends Controller
{
    use ApiResponse;
    public function __construct(private PasswordResetService $password_reset_service) {}
    public function forgot(ForgotPasswordRequest $request)
    {
        $this->password_reset_service->sendPasswordResetOtp($request->email);
        return $this->success([
            'message' => 'If the email exists and is verified, a password reset OTP has been sent.'
        ]);
    }

    public function reset(ResetNewPasswordRequest $request)
    {
        $this->password_reset_service->reset($request->email, $request->code, $request->password);

        return $this->success([
            'message' => 'Password has been reset successfully.'
        ]);
    }
}
