<?php

declare(strict_types=1);

namespace App\Actions\Scrape;

interface ScrapeHandlerInterface
{
    public function __invoke(): void;
}
