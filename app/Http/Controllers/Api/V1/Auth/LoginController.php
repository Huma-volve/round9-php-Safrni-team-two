<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;

class LoginController extends Controller
{
    use ApiResponse;
    public function __construct(private AuthService $authService) {}
    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request->email, $request->password);
        if (!$user->is_verified) {
            return $this->error('USER_NOT_VERIFIED', 'Please verify your email first.', null, 403);
        }
        $token = $this->authService->createToken($user);

        return $this->success([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
