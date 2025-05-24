<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Register the application's Artisan commands.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        // Run daily at 8am
        $schedule->command('offers:send-birthday')->dailyAt('08:00');

        // Delete sessions missing user_agent hourly
        $schedule->call(function () {
            DB::table('sessions')
                ->whereNull('user_agent')
                ->orWhere('user_agent', '')
                ->delete();
        })->hourly();

        // Run your sessions:prune command every 30 minutes
        $schedule->command('sessions:prune')->everyThirtyMinutes();
    }

    /**
     * Add manually registered Artisan commands here.
     */
    protected $commands = [
        \App\Console\Commands\ScanImageAttributions::class,
        \App\Console\Commands\PruneSessions::class, // add your prune command here
    ];
}