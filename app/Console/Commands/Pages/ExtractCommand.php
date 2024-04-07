<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Actions\Extract\ExtractAction;
use App\Enums\SiteName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class ExtractCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:extract {name?}';

    /**
     * @var string
     */
    protected $description = 'RawPageから情報を取得してPageを更新する';

    public function handle(ExtractAction $extractAction): int
    {
        try {
            $name = $this->argument('name');
            $siteName = is_string($name) ? SiteName::tryFrom($name) : null;

            $logger = Log::stack(['daily', 'stdout']);
            $extractAction($siteName, $logger);

            Cache::put('last_extract', now()->toDateTimeString());

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
