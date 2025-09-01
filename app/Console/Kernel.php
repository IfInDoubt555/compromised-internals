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
        // Birthday offer
        $schedule->command('offers:send-birthday')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->onOneServer()
            ->environments(['production']);

        // Sitemaps
        $schedule->command('sitemaps:build')
            ->dailyAt('03:15')
            ->withoutOverlapping()
            ->onOneServer()
            ->environments(['production']);

        // Clean sessions with missing UA
        $schedule->call(function () {
            DB::table('sessions')
                ->whereNull('user_agent')
                ->orWhere('user_agent', '')
                ->delete();
        })->hourly()
          ->withoutOverlapping()
          ->onOneServer()
          ->environments(['production']);

        // Built‑in sessions prune (plural)
        $schedule->command('sessions:prune')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->onOneServer()
            ->environments(['production']);

         // auto-publish anything due (safe to run every minute)
        $schedule->command('content:publish-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer()                         // if you ever add more servers
            ->appendOutputTo(storage_path('logs/scheduler.log')); // optional: debug log
        }

    /**
     * Add manually registered Artisan commands here.
     */
    protected $commands = [
        \App\Console\Commands\ScanImageAttributions::class,
        \App\Console\Commands\PruneSessions::class, // add your prune command here
        \App\Console\Commands\HistoryAddResults::class,
        \App\Console\Commands\SitemapWarmCommand::class,
        \App\Console\Commands\GenerateImageVariants::class,
    ];
}