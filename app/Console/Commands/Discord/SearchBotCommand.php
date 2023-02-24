<?php

namespace App\Console\Commands\Discord;

use App\Services\Discord\SearchBot;
use App\Services\Discord\TimeoutException;
use Illuminate\Console\Command;
use Throwable;

class SearchBotCommand extends Command
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
        try {
            app(SearchBot::class)->handle();
        } catch (TimeoutException $e) {
            $this->info($e->getMessage());

            return Command::SUCCESS;
        } catch (Throwable $th) {
            report($th);
            $this->error($th->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
