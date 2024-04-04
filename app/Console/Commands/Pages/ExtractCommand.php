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
    protected $signature = 'app:extract-command {name?}';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ExtractAction $extractAction): int
    {
        try {
            $name = $this->argument('name');
            $siteName = is_string($name) ? SiteName::tryFrom($name) : null;
            $extractAction($siteName);

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
