<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class ForceSameSiteNone
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response) {
            foreach ($response->headers->getCookies() as $cookie) {
                $cookie->withSecure(true); // Cookieのセキュリティを設定するための新しい方法を使用
                $cookie->withHttpOnly(true); // CookieのHTTPOnlyを設定するための新しい方法を使用
                $cookie->withSameSite('None'); // CookieのSameSiteを設定するための新しい方法を使用
            }
        }

        return $response;
    }
}
