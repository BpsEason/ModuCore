<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => '未經授權。請登入。'], 401);
        }
        $user = Auth::user();

        // Check if the user has the specified permission
        // This assumes your User model (or an extended trait) has a 'hasPermissionTo' method
        if (!method_exists($user, 'hasPermissionTo') || !$user->hasPermissionTo($permission)) {
            return response()->json(['message' => '沒有足夠的權限執行此操作。'], 403);
        }
        return $next($request);
    }
}
