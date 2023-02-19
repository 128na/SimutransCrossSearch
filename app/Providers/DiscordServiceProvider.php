<?php

namespace App\Providers;

use Discord\Discord;
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
        return [Discord::class];
    }

    public function register(): void
    {
        $this->app->singleton(Discord::class, function ($app) {
            // インスタンス作成時点から動き始めるので注意
            return new Discord([
                'token' => config('services.discord.token'),
                'intents' => Intents::getDefaultIntents(),
            ]);
        });
    }
}
