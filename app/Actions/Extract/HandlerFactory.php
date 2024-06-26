<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Actions\Extract\Japan\Handler as JapanHandler;
use App\Actions\Extract\Portal\Handler as PortalHandler;
use App\Actions\Extract\Twitrans\Handler as TwitransHandler;
use App\Enums\SiteName;
use Generator;

final class HandlerFactory
{
    /**
     * @param  array<int,SiteName>  $siteNames
     * @return Generator<int,HandlerInterface>
     */
    public function create(array $siteNames): Generator
    {
        foreach ($siteNames as $siteName) {
            yield match ($siteName) {
                SiteName::Japan => app(JapanHandler::class),
                SiteName::Portal => app(PortalHandler::class),
                SiteName::Twitrans => app(TwitransHandler::class),
            };
        }
    }
}
