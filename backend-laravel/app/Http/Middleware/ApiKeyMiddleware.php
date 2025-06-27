<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB; // For database interaction
use Illuminate\Support\Facades\Log; // For logging

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 檢查請求頭中是否包含 X-API-KEY
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            Log::warning('API Key missing for request', ['ip' => $request->ip(), 'path' => $request->path()]);
            return response()->json([
                'status' => 'error',
                'message' => 'API Key 是必需的。'
            ], 401);
        }

        // 從資料庫或配置中驗證 API Key
        // 建議在生產環境中使用更安全的 API Key 管理方式，例如使用 Hashed Keys
        // For simplicity, we are checking against a static env var or a simple DB table.
        // In a real app, you might fetch it from DB: DB::table('api_keys')->where('key', $apiKey)->where('is_active', true)->first();
        // Or for single API key: config('app.moducore_api_key') == $apiKey
        if (config('app.moducore_api_key') !== $apiKey) { // Using config for simple key check
            Log::warning('Invalid API Key provided', ['ip' => $request->ip(), 'path' => $request->path(), 'api_key_prefix' => substr($apiKey, 0, 10)]);
            return response()->json([
                'status' => 'error',
                'message' => '無效的 API Key。'
            ], 403);
        }

        // 如果 API Key 有效，則繼續處理請求
        return $next($request);
    }
}
