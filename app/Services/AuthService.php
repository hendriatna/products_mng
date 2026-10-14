<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create($data);

            $token = Auth::guard('api')->login($user);

            return [
                ...$this->formatTokenResponse($token),
                'user' => $user,
            ];
        });
    }

    public function login(array $credentials): array
    {
        $token = Auth::guard('api')->attempt($credentials);

        if (! $token) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        return $this->formatTokenResponse($token);
    }

    
    private function formatTokenResponse(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ];
    }
}