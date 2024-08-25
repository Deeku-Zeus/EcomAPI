<?php

namespace App\Providers;

use App\Auth\Providers\JwtAuthUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        //
        Auth::provider('api-jwt', fn($app, array $config) => new JwtAuthUserProvider());
    }
}
