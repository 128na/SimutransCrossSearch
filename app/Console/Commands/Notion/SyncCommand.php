<?php

declare(strict_types=1);

namespace App\Console\Commands\Notion;

use Illuminate\Console\Command;

final class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notion-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '入門サイトの新着DBと同期する';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
    }
}
