<?php

namespace App\Console;

use App\Helpers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping(10);

        // New day started at
        $time = Helpers::getSettingValueOf('day_started_at') ?? '00:00';

        $schedule->command('send:report')->dailyAt($time);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
