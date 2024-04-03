<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use Illuminate\Console\Command;

class ExtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-command';

    /**
     * The console command description.
     *
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
