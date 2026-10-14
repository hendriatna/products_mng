<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthTokenResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            $request->validated()
        );

        return (new AuthTokenResource($result))
            ->response()
            ->setStatusCode(201);
    }

    public function login(LoginRequest $request): AuthTokenResource
    {
        $result = $this->authService->login(
            $request->validated()
        );

        $result['user'] = auth('api')->user();

        return new AuthTokenResource($result);
    }
}