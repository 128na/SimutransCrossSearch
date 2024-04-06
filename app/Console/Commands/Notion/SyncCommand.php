<?php

declare(strict_types=1);

namespace App\Console\Commands\Notion;

use App\Actions\SyncNotion\SyncAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

final class SyncCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:sync-notion';

    /**
     * @var string
     */
    protected $description = '入門サイトの新着DBと同期する';

    public function handle(SyncAction $syncAction): int
    {
        logger('[SyncNotionDatabaseCommand] running');
        try {
            $databaseId = Config::string('services.notion.database_id', '');
            if (! $databaseId) {
                throw new Exception('databaseId not provided');
            }

            $syncAction($databaseId, 100);

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
