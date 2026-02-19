<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordResetService
{
    public function __construct(private OtpService $otpService) {}
    public function sendPasswordResetOtp(string $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user || !$user->is_verified) {
            return;
        }
        $created = null;

        DB::transaction(function () use ($user, $email, &$created) {
            $created = $this->otpService->create($email, 'password_reset', $user);
        });

        // Why? Because we want to ensure that the OTP is only sent if the database transaction commits successfully and Otp Created successfully.
        DB::afterCommit(function () use ($created, $email) {
            $this->otpService->sendNotification($email, $created['code'], 'password_reset');
        });
    }


    /**
     * Verify the
     * @param string $email
     * @param string $code
     * @param string $newPassword
     * @return void
     */
    public function reset(string $email, string $code, string $newPassword): void
    {
        DB::transaction(function () use ($email, $code, $newPassword) {
            // Verify OTP and mark it as used
            $this->otpService->verify($email, 'password_reset', $code);

            $user = User::where('email', $email)->first();

            if (!$user) {
                throw ValidationException::withMessages(['email' => ['Invalid email.']]);
            }

            $user->update(['password' => Hash::make($newPassword)]);
        });
    }
}
