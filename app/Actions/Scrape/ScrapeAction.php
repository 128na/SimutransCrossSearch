<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

use App\Enums\SiteName;
use Psr\Log\LoggerInterface;

final readonly class ScrapeAction
{
    public function __construct(
        private HandlerFactory $handlerFactory
    ) {}

    public function __invoke(?SiteName $siteName, LoggerInterface $logger): void
    {
        $siteNames = $siteName instanceof SiteName ? [$siteName] : SiteName::cases();

        foreach ($this->handlerFactory->create($siteNames) as $index => $handler) {
            try {
                $handler($logger);
            } catch (\Throwable $th) {
                $logger->error('site failed', [$siteNames[$index]->value, $th]);
            }
        }
    }
}
