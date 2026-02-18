<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private OtpService $otpService) {}

    /**
     * Registers a new user with the given name, email, and password.
     * After registration, an OTP code is sent to the user's email address
     * to verify the email address. The user is not verified until the
     * OTP code is verified.
     */
    public function register(string $name, string $email, string $password): User
    {
        $createdData = DB::transaction(function () use ($name, $email, $password) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'user',
                'is_verified' => false
            ]);

            $createdData = $this->otpService->create($email, 'email_verify', $user);

            return $createdData;
        });

        $user = $createdData['user'];
        $otpCode = $createdData['code'];
        $expiryMinutes = config('otp.expires_minutes');

        // Send OTP notification after transaction commits to ensure user is created before sending notification
        DB::afterCommit(function () use ($email, $createdData) {
            $this->otpService->sendNotification($email, $createdData['code'], 'email_verify');
        });

        return $user;
    }

    /**
     * Logs in a user and returns the user object if the credentials are valid.
     * If the credentials are invalid, a ValidationException is thrown.
     *
     * @param string $email
     * @param string $password
     * @return User|null
     * @throws ValidationException
     */
    public function login(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }
        return $user;
    }

    /**
     * Generate an API token for the given user.
     *
     * @param User $user
     * @return string
     */
    public function createToken(User $user): string
    {
        return $user->createToken('api')->plainTextToken;
    }
}
