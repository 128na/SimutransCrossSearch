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
    protected $signature = 'discord:bot';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
    ) {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle()
    {
        $this->setMaxExecutionTime();
        app(SearchBotService::class)->handle();

        return Command::SUCCESS;
    }

    private function setMaxExecutionTime(): void
    {
        if (App::environment('production')) {
            $this->info('max_execution_time is '.config('app.command.max_execution_time'));
            set_time_limit(config('app.command.max_execution_time'));
        }
    }
}
