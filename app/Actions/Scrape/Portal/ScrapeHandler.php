<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Portal;

use App\Actions\Scrape\ScrapeHandlerInterface;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Enums\SiteName;

class ScrapeHandler implements ScrapeHandlerInterface
{
    public function __construct(
        private readonly CursorUrl $cursorUrl,
        private readonly UpdateOrCreateRawPage $updateOrCreateRawPage,
    ) {

    }

    public function __invoke(): void
    {
        foreach (($this->cursorUrl)() as $url) {
            ($this->updateOrCreateRawPage)(
                $url,
                SiteName::Portal,
                ''
            );
        }
    }
}
