<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:page-scrape')->dailyAt('1:00')->withoutOverlapping()->onOneServer();
Schedule::command('app:page-extract')->dailyAt('3:00')->withoutOverlapping()->onOneServer();

Schedule::command('app:notion-sync')->dailyAt('7:00')->withoutOverlapping()->onOneServer();
