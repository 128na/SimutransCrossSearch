<?php

namespace App\Console\Commands\Discord;

use App\Services\Discord\SearchBotService;
use Illuminate\Console\Command;

class SearchBot extends Command
{
    /**
     * @var string
     */
    protected $signature = 'discord:search_bot';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
    ) {
        set_time_limit(config('app.command.max_execution_time'));
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle()
    {
        app(SearchBotService::class)->handle();

        return Command::SUCCESS;
    }
}
