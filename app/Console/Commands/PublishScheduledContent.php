<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Thread;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled';
    protected $description = 'Publish posts/threads whose scheduled_for time has passed';

    public function handle(): int
    {
        $now = now()->utc();

        Post::where('status','scheduled')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for','<=',$now)
            ->chunkById(200, function ($chunk) use ($now) {
                foreach ($chunk as $p) {
                    $p->forceFill(['status'=>'published','published_at'=>$now,'scheduled_for'=>null])->save();
                }
            });

        Thread::where('status','scheduled')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for','<=',$now)
            ->chunkById(200, function ($chunk) use ($now) {
                foreach ($chunk as $t) {
                    $t->forceFill(['status'=>'published','published_at'=>$now,'scheduled_for'=>null])->save();
                }
            });

        return self::SUCCESS;
    }
}