<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Actions\Scrape\JapanWiki\ScrapeHandler as JapanWikiScrapeHandler;
use App\Actions\Scrape\Portal\ScrapeHandler as PortalScrapeHandler;
use App\Actions\Scrape\Twitrans\ScrapeHandler as TwitransScrapeHandler;
use App\Enums\SiteName;
use Generator;

class ScrapeHandlerFactory
{
    /**
     * @param  array<int,SiteName>  $siteNames
     * @return Generator<int,\App\Actions\Scrape\ScrapeHandlerInterface>
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
