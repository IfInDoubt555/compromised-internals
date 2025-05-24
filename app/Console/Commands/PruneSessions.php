<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneSessions extends Command
{
    protected $signature = 'sessions:prune';
    protected $description = 'Deletes expired sessions from the database';

    public function handle()
    {
        $lifetime = min(config('session.lifetime'), 30) * 60;

        DB::table('sessions')
            ->where('last_activity', '<', time() - $lifetime)
            ->delete();

        $this->info('Expired sessions pruned successfully.');
    }
}
