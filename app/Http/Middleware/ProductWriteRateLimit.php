<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ProductWriteRateLimit
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $userId = $request->user('api')?->getAuthIdentifier()
            ?? $request->ip();

        $routeName = $request->route()?->getName() ?? 'unknown';

        $key = "product-write:{$userId}:{$routeName}";

        if (RateLimiter::tooManyAttempts($key, 1)) {
            return response()->json([
                'message' => 'Too many requests. Please try again in 5 seconds.',
            ], 429)->header(
                'Retry-After',
                (string) RateLimiter::availableIn($key)
            );
        }

        RateLimiter::hit($key, 5);

        return $next($request);
    }
}