<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the application's Artisan commands.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        // Run daily at 8am
        $schedule->command('offers:send-birthday')->dailyAt('08:00');
    }

    /**
     * Add manually registered Artisan commands here.
     */
    protected $commands = [
        \App\Console\Commands\ScanImageAttributions::class,
    ];
}
