<?php

namespace App\Http\Controllers\Traits\Auth;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;

trait AuthResponse
{
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
    protected function respondWithCookie($token)
    {
        $time = time() + 30 * 60 * 60 * 24;
        $cookie = Cookie::create('auth_token')
            ->withValue($token)
            ->withExpires($time)
            ->withSameSite('none');
        return response([
            'access_token' => $token,
            'message' => 'success',
            'expires_in' => $time
        ])
            ->withCookie($cookie);
    }
}
