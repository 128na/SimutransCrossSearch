<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 1 min
        $schedule->command('page:scrape portal')->dailyAt('0:00');

        // 15 min
        $schedule->command('page:scrape twitrans')->dailyAt('1:00');

        // 15 min
        $schedule->command('page:scrape japan')->dailyAt('2:00');

        $schedule->command('page:extract portal')->dailyAt('3:00');
        $schedule->command('page:extract twitrans')->dailyAt('3:00');
        $schedule->command('page:extract japan')->dailyAt('3:00');

        // 1 min
        $schedule->command('media:fetch youtube')->dailyAt('4:00');

        $schedule->command('backup:clean')->dailyAt('5:00');
        $schedule->command('backup:run')->dailyAt('6:00');

        // 1 min APIデータ更新は毎日5時
        $schedule->command('media:fetch nico')->dailyAt('6:00');

        // 10 min 8hサイクル
        $schedule->command('media:fetch twitter')->cron('0 5,13,21 * * *');
        $schedule->command('tweet:summary')->dailyAt('7:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
