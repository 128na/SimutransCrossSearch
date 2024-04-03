<?php

namespace App\Providers;

use App\Services\Discord\TimeoutableDiscord;
use Discord\WebSockets\Intents;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<string>
     */
    public function provides()
    {
        return [TimeoutableDiscord::class];
    }

    public function register(): void
    {
        $this->app->singleton(TimeoutableDiscord::class, function ($app) {
            // インスタンス作成時点から動き始めるので注意
            return new TimeoutableDiscord([
                'token' => config('services.discord.token'),
                'intents' => Intents::getDefaultIntents(),
            ], config('services.discord.timeout'));
        });
    }
}
