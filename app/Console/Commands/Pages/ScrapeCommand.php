<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Actions\Scrape\ScrapeAction;
use App\Enums\SiteName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:scrape {name?}';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ScrapeAction $scrapeAction): int
    {
        try {
            $name = $this->argument('name');
            $siteName = is_string($name) ? SiteName::tryFrom($name) : null;

            $logger = Log::stack(['daily', 'stdout']);
            $scrapeAction($siteName, $logger);

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());
            $this->error($throwable->getTraceAsString());

            return self::FAILURE;
        }
    }
}
