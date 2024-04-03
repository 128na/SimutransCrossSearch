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
    protected $signature = 'app:page-scrape {?name}';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ScrapeAction $scrapeAction): int
    {
        try {
            $siteName = SiteName::tryFrom($this->argument('name', ''));
            $scrapeAction($siteName);

            return self::SUCCESS;
        } catch (\Throwable $th) {
            report($th);

            return self::FAILURE;
        }
    }
}
