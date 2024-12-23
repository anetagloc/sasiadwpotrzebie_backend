<?php

namespace App\Providers;

use App\Models\Rating;
use Illuminate\Http\Request;
use App\Policies\RatingPolicy;
use Illuminate\Support\Facades\Gate;
use App\Services\UserMatchingService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserMatchingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        Gate::policy(Rating::class, RatingPolicy::class);
    }
}
