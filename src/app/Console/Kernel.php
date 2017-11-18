<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
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
    $schedule->call(function () {
      DB::table('scrape:site JapaneseSimutrans')->delete();
    })->dailyAt('1:03');
    $schedule->call(function () {
      DB::table('scrape:site Twitrans')->delete();
    })->dailyAt('2:33');
    $schedule->call(function () {
      DB::table('scrape:site SimutransPortal')->delete();
    })->dailyAt('3:34');
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
