<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * ハンドルリクエスト
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int $roleId
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roleId)
    {
        $user = Auth::user();

        // ユーザーが認証され、role_idが指定したものと一致するか確認
        if ($user && $user->role_id == $roleId) {
            return $next($request);
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }
}
