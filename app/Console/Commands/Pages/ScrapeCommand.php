<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Actions\Scrape\ScrapeAction;
use App\Enums\SiteName;
use Illuminate\Console\Command;

class ScrapeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:page-scrape {name?}';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ScrapeAction $scrapeAction): int
    {
        try {
            $name = $this->argument('name');
            $siteName = is_string($name) ? SiteName::tryFrom($name) : null;
            $scrapeAction($siteName);

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
