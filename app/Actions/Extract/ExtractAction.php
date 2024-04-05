<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Enums\SiteName;

class ExtractAction
{
    public function __construct(
        private readonly HandlerFactory $handlerFactory
    ) {
    }

    public function __invoke(?SiteName $siteName = null): void
    {
        $siteNames = $siteName instanceof SiteName ? [$siteName] : SiteName::cases();

        foreach ($this->handlerFactory->create($siteNames) as $handler) {
            $handler();
        }
    }
}
