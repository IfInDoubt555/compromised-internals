<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Thread;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled {--limit=200 : Max items per chunk}';
    protected $description = 'Publish posts/threads whose scheduled_for time has passed';

    public function handle(): int
    {
        $now = now()->utc();
        $limit = (int) $this->option('limit');

        // POSTS
        Post::where('status', 'scheduled')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->chunkById($limit, function ($chunk) use ($now) {
                foreach ($chunk as $p) {
                    $p->forceFill([
                        'status'          => 'published',
                        'publish_status'  => 'published',   // keep legacy column in sync
                        'published_at'    => $now,
                        'scheduled_for'   => null,
                    ])->save();
                    $this->line("Published post #{$p->id} {$p->title}");
                }
            });

        // THREADS
        Thread::where('status', 'scheduled')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->chunkById($limit, function ($chunk) use ($now) {
                foreach ($chunk as $t) {
                    $t->forceFill([
                        'status'         => 'published',
                        'published_at'   => $now,
                        'scheduled_for'  => null,
                    ])->save();
                    $this->line("Published thread #{$t->id} {$t->title}");
                }
            });

        $this->info('Done.');
        return self::SUCCESS;
    }
}