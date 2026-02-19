<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function create(string $email, string $purpose, ?User $user = null): array
    {
        $this->throttleSend($email, $purpose);

        $code = $this->generateCode();
        $expiresAt = now()->addMinutes((int) config('otp.expires_minutes'));

        $otp = OtpCode::create([
            'user_id' => $user?->id,
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => $expiresAt,
        ]);

        return [
            'otp' => $otp,
            'code' => $code,          // plaintext code to be sent via notification
            'expires_at' => $expiresAt,
            'user' => $user ?? null
        ];
    }

    /**
     * Verify an OTP code.
     *
     * @param string $email The email address of the user.
     * @param string $purpose The purpose of the OTP code.
     * @param string $code The plaintext OTP code.
     *
     * @return OtpCode The verified OTP code.
     *
     * @throws ValidationException If the OTP code is not found, already used, expired, or invalid.
     */
    public function verify(string $email, string $purpose, string $code): OtpCode
    {
        $this->throttleVerify($email, $purpose);

        $otp = OtpCode::where('email', $email)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (!$otp) {
            throw ValidationException::withMessages(['code' => ['OTP not found.']]);
        }
        if ($otp->used_at) {
            throw ValidationException::withMessages(['code' => ['OTP already used.']]);
        }
        if (now()->greaterThan($otp->expires_at)) {
            throw ValidationException::withMessages(['code' => ['OTP expired.']]);
        }
        if (!Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            throw ValidationException::withMessages(['code' => ['OTP is invalid.']]);
        }

        $otp->markAsUsed();
        return $otp;
    }

    /**
     * Send OTP notification via email only.
     */
    public function sendNotification(string $email, string $code, string $purpose): void
    {
        Notification::route('mail', $email)->notify(
            new OtpNotification($code, $purpose, (int) config('otp.expires_minutes'))
        );
    }

    /**
     * Create otp + send mail AFTER commit (safe by default).
     * Use this outside transactions or together with DB::afterCommit().
     */
    public function send(string $email, string $purpose, ?User $user = null): OtpCode
    {
        // create OTP record in transaction to ensure data integrity
        $created = $this->create($email, $purpose, $user);

        // Safe default: only email after commit
        DB::afterCommit(function () use ($email, $created, $purpose) {
            $this->sendNotification($email, $created['code'], $purpose);
        });

        return $created['otp'];
    }


    public function invalidatePrevious(string $email, string $purpose): void
    {
        OtpCode::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }

    public function resend(string $email, string $purpose, ?User $user = null): void
    {
        if ($purpose === 'email_verify' && $user && $user->is_verified) {
            throw ValidationException::withMessages([
                'email' => ['Email is already verified.']
            ]);
        }

        $created = DB::transaction(function () use ($email, $purpose, $user) {
            // invalidate previous un-used codes for same purpose
            $this->invalidatePrevious($email, $purpose);

            // create new OTP record
            return $this->create($email, $purpose, $user);
        });

        // send after commit
        DB::afterCommit(function () use ($email, $created, $purpose) {
            $this->sendNotification($email, $created['code'], $purpose);
        });
    }



    // Helper methods for rate limiting and code generation
    private function throttleSend(string $email, string $purpose): void
    {
        $max = (int) config('otp.resend_rate_limit_per_minute', 2);
        $key = "otp:send:{$purpose}:{$email}";

        $this->throttle($key, $max, 60);
    }

    private function throttleVerify(string $email, string $purpose): void
    {
        $max = (int) config('otp.verify_attempts_per_minute', 5);
        $key = "otp:verify:{$purpose}:{$email}";

        $this->throttle($key, $max, 60);
    }

    private function throttle(string $key, int $maxAttempts, int $decaySeconds): void
    {
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'rate_limit' => ["Too many attempts. Try again in {$seconds} seconds."]
            ]);
        }

        RateLimiter::hit($key, $decaySeconds);
    }

    private function generateCode(): string
    {
        $length = config('otp.length');

        return (string)random_int(pow(10, $length - 1), pow(10, $length) - 1);
    }
}
