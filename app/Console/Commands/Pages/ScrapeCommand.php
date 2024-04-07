<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Actions\Scrape\ScrapeAction;
use App\Enums\SiteName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class ScrapeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:scrape {name?}';

    /**
     * @var string
     */
    protected $description = 'サイトからHTMLを取得してRawPageを更新する';

    public function handle(ScrapeAction $scrapeAction): int
    {
        try {
            $name = $this->argument('name');
            $siteName = is_string($name) ? SiteName::tryFrom($name) : null;

            $logger = Log::stack(['daily', 'stdout']);
            $scrapeAction($siteName, $logger);

            Cache::put('last_crawl', now()->toDateTimeString());

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
