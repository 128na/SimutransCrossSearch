<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Actions\Scrape\Japan\Handler as JapanHandler;
use App\Actions\Scrape\Portal\Handler as PortalHandler;
use App\Actions\Scrape\Twitrans\Handler as TwitransHandler;
use App\Enums\SiteName;
use Generator;

final class HandlerFactory
{
    /**
     * @param  array<int,SiteName>  $siteNames
     * @return Generator<int,\App\Actions\Scrape\HandlerInterface>
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
