<?php

namespace App\Console\Commands\Discord;

use App\Services\Discord\SearchBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

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
        $this->setMaxExecutionTime();
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

    private function setMaxExecutionTime(): void
    {
        if (App::environment('production')) {
            set_time_limit(config('app.command.max_execution_time'));
        }
    }
}
