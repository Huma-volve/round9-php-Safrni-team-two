<?php

namespace App\Services;

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoogleAuthService
{
    /**
     * Returns an authorization URL for client to open with proper parameters
     *
     * @param string $redirectUri URL to redirect the user to after authorization.
     * @param string $codeChallenge The code challenge.
     * @param string $state The state.
     *
     */
    public function getGoogleAuthUrl(string $redirectUri, string $codeChallenge, string $state): string
    {
        $params = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'plain',
            'state' => $state,
            'prompt' => 'consent',
        ]);

        return "https://accounts.google.com/o/oauth2/v2/auth?{$params}";
    }


    /**
     * SPA sends code + code_verifier.
     * Backend exchanges with Google, validates id_token, then finds/creates user, links identity.
     */

    public function exchangeCodeForToken(string $code, string $codeVerifier, string $redirectUri)
    {
        $tokens = $this->exchangeCode($code, $codeVerifier, $redirectUri);

        $claims = $this->tokenInfo($tokens['id_token'] ?? null);

        // Double check
        if ($claims['aud'] && $claims['aud'] !== config('services.google.client_id')) {
            throw ValidationException::withMessages(['google' => ['Invalid token audience.']]);
        }

        $googleSub = $claims['sub'];
        $email = $claims['email'];
        $emailVerified = (bool)($claims['email_verified'] ?? false);
        $name = $claims['name'] ?? 'User';

        if (!$emailVerified) {
            throw ValidationException::withMessages(['google' => ['Google email is not verified.']]);
        }


        //Already Exists?
        $identity = SocialIdentity::where('provider', 'google')
            ->where('provider_user_id', $googleSub)
            ->first();
        if ($identity) {
            $user = $identity->user;

            if (!$user->is_verified) {
                $user->update(['is_verified' => true]);
            }
            return $user;
        }

        // Search by email if not linked.
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
                'is_verified' => true,
            ]);
        } else {
            if (!$user->is_verified) {
                $user->update(['is_verified' => true]);
            }
        }
        SocialIdentity::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => $googleSub,
            'email' => $email,
            'raw' => $claims,
        ]);

        return $user;
    }

    private function exchangeCode(string $code, string $codeVerifier, string $redirectUri): array
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'code' => $code,
            'code_verifier' => $codeVerifier,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (!$response->ok()) {
            throw ValidationException::withMessages([
                'google' => 'Google code exchange failed.'
            ]);
        }
        return $response->json();
    }

    private function tokenInfo(?string $idToken): array
    {
        if (!$idToken) {
            throw ValidationException::withMessages(['google' => ['Missing id_token from Google.']]);
        }

        $info = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken
        ]);

        if (!$info->ok()) {
            throw ValidationException::withMessages(['google' => ['Invalid id_token.']]);
        }

        return $info->json();
    }
}
