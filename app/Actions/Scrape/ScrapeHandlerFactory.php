<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Actions\Scrape\Handlers\JapanWikiScrapeHandler;
use App\Actions\Scrape\Handlers\PortalScrapeHandler;
use App\Actions\Scrape\Handlers\TwitransScrapeHandler;
use App\Enums\SiteName;
use Generator;

class ScrapeHandlerFactory
{
    /**
     * @param  array<int,SiteName>  $siteNames
     * @return Generator<\App\Actions\Scrape\Handlers\ScrapeHandlerInterface>
     */
    public function create(array $siteNames): Generator
    {
        foreach ($siteNames as $siteName) {
            yield match ($siteName) {
                SiteName::SimutransJapanWiki => app(JapanWikiScrapeHandler::class),
                SiteName::TwitransWiki => app(TwitransScrapeHandler::class),
                SiteName::SimutransAddonPortal => app(PortalScrapeHandler::class),
            };
        }
    }
}
