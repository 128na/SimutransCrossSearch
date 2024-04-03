<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Actions\Extract\ExtractAction;
use App\Enums\SiteName;
use Illuminate\Console\Command;

class ExtractCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:extract-command';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ExtractAction $extractAction): int
    {
        try {
            $siteName = SiteName::tryFrom($this->argument('name', ''));
            $extractAction($siteName);

            return self::SUCCESS;
        } catch (\Throwable $th) {
            report($th);

            return self::FAILURE;
        }
    }
}
