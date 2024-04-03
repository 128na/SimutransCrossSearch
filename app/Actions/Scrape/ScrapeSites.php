<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\SiteName;

class ScrapeSites
{
    public function __construct(
        private readonly ScrapeHandlerFactory $scrapeHandlerFactory
    ) {
    }

    public function __invoke(?SiteName $siteName = null): void
    {
        $siteNames = $siteName instanceof SiteName ? [$siteName] : SiteName::cases();

        foreach ($this->scrapeHandlerFactory->create($siteNames) as $handler) {
            $handler();
        }
    }
}
