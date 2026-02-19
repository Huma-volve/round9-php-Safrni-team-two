<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class RegisterController extends Controller
{
    use ApiResponse;
    public function __construct(private AuthService $authService) {}
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register(
            $request->name,
            $request->email,
            $request->password
        );

        return $this->success([
            'user' => $user,
            'message' => 'Registered. OTP sent to email for verification.',
        ], 201);
    }
}
