<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1): Response
    {
        $key = 'rate_limit:' . ($request->user()->id ?? $request->ip());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $response = response()->json([
                'status' => 'error',
                'message' => '請求過於頻繁。請稍後再試。'
            ], 429);

            $retryAfter = RateLimiter::availableIn($key);
            $response->headers->add([
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
                'Retry-After' => $retryAfter,
            ]);

            return $response;
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::retriesLeft($key, $maxAttempts),
        ]);

        return $response;
    }
}
