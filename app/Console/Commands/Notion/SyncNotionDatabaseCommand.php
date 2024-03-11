<?php

namespace App\Console\Commands\Notion;

use App\Services\Notion\NotionService;
use Illuminate\Console\Command;

class SyncNotionDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notion:sync-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(NotionService $notionService)
    {
        $databaseId = config('services.notion.database_id');
        $notionService->sync($databaseId, 100);
    }
}
