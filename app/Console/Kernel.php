<?php

namespace App\Console;

use App\Models\ScheduleLog;
use App\Services\ScheduleLogService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var ScheduleLogService
     */
    private $service;

    public function __construct(Application $app, Dispatcher $events, ScheduleLogService $service)
    {
        parent::__construct($app, $events);
        $this->service = $service;
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 1 min
        $this->appendLogging($schedule->command('page:scrape portal')->dailyAt('0:30'), 'scrape-portal');
        $this->appendLogging($schedule->command('page:extract portal')->dailyAt('0:35'), 'extract-portal');

        // 15 min
        $this->appendLogging($schedule->command('page:scrape twitrans')->dailyAt('1:00'), 'scrape-twitrans');
        $this->appendLogging($schedule->command('page:extract twitrans')->dailyAt('1:30'), 'extract-twitrans');

        // 15 min
        $this->appendLogging($schedule->command('page:scrape japan')->dailyAt('2:00'), 'scrape-japan');
        $this->appendLogging($schedule->command('page:extract japan')->dailyAt('2:30'), 'extract-japan');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    private function appendLogging($schedule, $name)
    {
        $schedule
            ->before(function () use ($name) {
                $this->service->begin($name);
            })
            ->onSuccess(function () use ($name) {
                $this->service->end($name);
            })
            ->emailOutputOnFailure(config('mail.cron.address'));
    }
}
