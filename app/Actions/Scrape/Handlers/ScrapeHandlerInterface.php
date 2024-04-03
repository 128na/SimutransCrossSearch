<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Handlers;

interface ScrapeHandlerInterface
{
    public function __invoke(): void;
}
