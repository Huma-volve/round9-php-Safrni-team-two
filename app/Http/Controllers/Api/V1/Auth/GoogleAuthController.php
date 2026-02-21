<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GoogleExchangeRequest;
use App\Http\Requests\Auth\GoogleUrlRequest;
use App\Services\AuthService;
use App\Services\GoogleAuthService;
use App\Traits\ApiResponse;

class GoogleAuthController extends Controller
{
    use ApiResponse;
    public function __construct(private GoogleAuthService $googleAuthService, private AuthService $authService) {}
    public function url(GoogleUrlRequest $request)
    {
        $url = $this->googleAuthService->getGoogleAuthUrl($request->redirect_uri, $request->code_challenge, $request->state);

        return $this->success([
            'url' => $url
        ]);
    }

    /**
     * Exchange code + login/register + issue Sanctum token
     * @param GoogleExchangeRequest $request
     */
    public function exchange(GoogleExchangeRequest $request)
    {
        $user = $this->googleAuthService->exchangeCodeForToken($request->code, $request->code_verifier, $request->redirect_uri);
        $token = $this->authService->createToken($user);

        return $this->success([
            'token' => $token,
            'user' => $user
        ]);
    }
}
