<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cookie;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    Cookie::macro('setSecureSameSiteNone', function ($name, $value, $minutes = 0, $path = null, $domain = null, $secure = true, $httpOnly = true) {
        return Cookie::make($name, $value, $minutes, $path, $domain, $secure, $httpOnly, false, 'none');
    });

    // すべてのレスポンスクッキーにSameSite=Noneを適用
    $this->app->resolving(\Illuminate\Contracts\Http\Kernel::class, function ($kernel) {
        $kernel->prependMiddleware(\App\Http\Middleware\ForceSameSiteNone::class);
    });
}
}
