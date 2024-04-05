<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\SiteName;
use Psr\Log\LoggerInterface;

class ScrapeAction
{
    public function __construct(
        private readonly HandlerFactory $handlerFactory
    ) {
    }

    public function __invoke(?SiteName $siteName, LoggerInterface $logger): void
    {
        $siteNames = $siteName instanceof SiteName ? [$siteName] : SiteName::cases();

        foreach ($this->handlerFactory->create($siteNames) as $handler) {
            $handler($logger);
        }
    }
}
