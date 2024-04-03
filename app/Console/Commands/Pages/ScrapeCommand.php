<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

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

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return self::SUCCESS;
    }
}
