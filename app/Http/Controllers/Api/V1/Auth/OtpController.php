<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\ApiResponse;

class OtpController extends Controller
{
    use ApiResponse;
    public function __construct(private OtpService $otpService) {}
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otp = $this->otpService->verify($request->email, $request->purpose, $request->code);
        if ($request->purpose === 'email_verify') {
            User::where('email', $request->email)->update(['is_verified' => true]);
        }
        return $this->success(['message' => 'OTP verified successfully.'], 200);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $this->otpService->resend($request->email, $request->purpose, $user);
        return $this->success(['message' => 'OTP resent successfully.'], 200);
    }
}
