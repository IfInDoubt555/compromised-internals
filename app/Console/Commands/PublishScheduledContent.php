<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Thread;
use Carbon\CarbonImmutable;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled
                            {--limit=200 : Max items processed per chunk}
                            {--dry : Show what would be published without saving}';

    protected $description = 'Publish posts/threads whose scheduled_for time has passed.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $dry   = (bool) $this->option('dry');
        $now   = CarbonImmutable::now(); // honors APP_TIMEZONE

        $this->info(sprintf(
            'Running at %s (tz: %s)%s',
            $now->toDateTimeString(),
            config('app.timezone'),
            $dry ? ' — DRY RUN' : ''
        ));

        $publishedPosts  = $this->publishPostsDue($now, $limit, $dry);
        $publishedThreads = $this->publishThreadsDue($now, $limit, $dry);

        $this->newLine();
        $this->info("Done. Posts published: {$publishedPosts}; Threads published: {$publishedThreads}.");

        return self::SUCCESS;
    }

    /**
     * Publish scheduled posts.
     */
    private function publishPostsDue(CarbonImmutable $now, int $limit, bool $dry): int
    {
        $count = 0;

        Post::query()
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->orderBy('scheduled_for')
            ->chunkById($limit, function ($chunk) use (&$count, $now, $dry) {
                foreach ($chunk as $p) {
                    $this->line(sprintf('[POST] #%d | %s | due %s',
                        $p->id,
                        str($p->title)->limit(80),
                        optional($p->scheduled_for)->toDateTimeString()
                    ));

                    if ($dry) {
                        continue;
                    }

                    $p->forceFill([
                        'status'         => 'published',
                        'publish_status' => 'published', // keep legacy in sync
                        // keep the scheduled time as the official publish time if it exists, else now
                        'published_at'   => $p->scheduled_for ?: $now,
                        'scheduled_for'  => null,
                    ])->save();

                    $count++;
                }
            }, $column = 'id', $alias = 'id');

        return $count;
    }

    /**
     * Publish scheduled threads.
     */
    private function publishThreadsDue(CarbonImmutable $now, int $limit, bool $dry): int
    {
        $count = 0;

        Thread::query()
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->orderBy('scheduled_for')
            ->chunkById($limit, function ($chunk) use (&$count, $now, $dry) {
                foreach ($chunk as $t) {
                    $this->line(sprintf('[THREAD] #%d | %s | due %s',
                        $t->id,
                        str($t->title)->limit(80),
                        optional($t->scheduled_for)->toDateTimeString()
                    ));

                    if ($dry) {
                        continue;
                    }

                    $t->forceFill([
                        'status'        => 'published',
                        'published_at'  => $t->scheduled_for ?: $now,
                        'scheduled_for' => null,
                    ])->save();

                    $count++;
                }
            }, $column = 'id', $alias = 'id');

        return $count;
    }
}