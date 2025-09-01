<?php

namespace App\Console\Commands;

use App\Jobs\GenerateImageVariants as GenerateVariantsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateImageVariants extends Command
{
    protected $signature = 'images:generate
        {--disk=public : Storage disk}
        {--prefix=posts : Directory prefix, e.g. profile_pics or posts}
        {--sizes=160,320,640,960,1280 : Comma-separated widths (auto 80,160,320 for profile* if untouched)}
        {--formats=webp,avif : Comma-separated formats}
        {--sync : Run synchronously instead of queueing}';

    protected $description = 'Generate responsive image variants for files under a prefix';

    public function handle(): int
    {
        $disk   = (string) $this->option('disk');
        $prefix = trim((string) $this->option('prefix'), '/');

        if (!Storage::disk($disk)->exists($prefix)) {
            $this->warn("No folder: {$disk}:{$prefix}");
            return self::SUCCESS;
        }

        // If user didn’t override sizes and we’re in a profile folder, use small defaults.
        $rawSizesDefault = '160,320,640,960,1280';
        $rawSizes = (string) $this->option('sizes');
        if ($rawSizes === $rawSizesDefault && Str::startsWith($prefix, ['profile', 'avatars', 'profile_pics'])) {
            $rawSizes = '80,160,320';
            $this->line('Auto sizes for profile pics: 80,160,320');
        }

        $sizes   = collect(explode(',', $rawSizes))->map(fn ($w) => (int) trim($w))->filter();
        $formats = collect(explode(',', (string) $this->option('formats')))->map(fn ($f) => strtolower(trim($f)))->filter();
        $sync    = (bool) $this->option('sync');

        // Find candidate originals (skip files already being variants like foo-160.jpg)
        $files = collect(Storage::disk($disk)->allFiles($prefix))
            ->filter(function (string $p) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'avif'], true)) {
                    return false;
                }
                // Skip sources that are already width-suffixed (…-160.ext)
                $base = pathinfo($p, PATHINFO_FILENAME);
                return !preg_match('/-\d+$/', $base);
            })
            ->values();

        if ($files->isEmpty()) {
            $this->info('No source files found.');
            return self::SUCCESS;
        }

        $this->info("Processing {$files->count()} files on disk={$disk}, prefix={$prefix}");
        $bar = $this->output->createProgressBar($files->count());
        $bar->start();

        $queued = 0;
        $done   = 0;

        foreach ($files as $rel) {
            if ($sync) {
                // run now
                GenerateVariantsJob::dispatchSync($disk, $rel, $sizes->all(), $formats->all());
                $done++;
            } else {
                // queue on dedicated queue
                GenerateVariantsJob::dispatch($disk, $rel, $sizes->all(), $formats->all())->onQueue('images');
                $queued++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info($sync
            ? "Done. Generated variants for {$done} file(s)."
            : "Queued {$queued} job(s) on the 'images' queue."
        );

        return self::SUCCESS;
    }
}