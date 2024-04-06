<?php

declare(strict_types=1);

namespace App\Console\Commands\Notion;

use App\Actions\SyncNotion\SyncAction;
use Illuminate\Console\Command;

final class SyncCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:notion-sync';

    /**
     * @var string
     */
    protected $description = '入門サイトの新着DBと同期する';

    public function handle(SyncAction $syncAction): int
    {
        logger('[SyncNotionDatabaseCommand] running');
        try {
            $databaseId = config('services.notion.database_id');
            $syncAction($databaseId, 100);

            return self::SUCCESS;
        } catch (\Throwable $th) {
            report($th);

            return self::FAILURE;
        }
    }
}
