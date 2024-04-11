<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Notion\Notion;
use RateLimiter;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(Notion::class, fn (Application $application): Notion => Notion::create(Config::string('services.notion.secret', '')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }
}
