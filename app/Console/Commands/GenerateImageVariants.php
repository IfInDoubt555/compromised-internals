<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;
use Throwable;

class GenerateImageVariants extends Command
{
    protected $signature = 'images:generate
        {--disk=public}
        {--prefix=posts}
        {--sizes=160,320,640,960,1280}
        {--formats=webp,avif}
        {--quality=82}';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $dir  = trim($this->option('prefix'), '/');
        if (!Storage::disk($disk)->exists($dir)) {
            $this->warn("No folder: {$disk}:{$dir}");
            return self::SUCCESS;
        }

        $sizes   = collect(explode(',', $this->option('sizes')))->map(fn($w)=>(int)trim($w));
        $formats = collect(explode(',', $this->option('formats')))->map(fn($f)=>strtolower(trim($f)));
        $quality = (int)$this->option('quality');

        $files = collect(Storage::disk($disk)->allFiles($dir))
            ->filter(fn($p)=>preg_match('/\.(png|jpe?g)$/i', $p));

        foreach ($files as $rel) {
            $abs = Storage::disk($disk)->path($rel);
            $name = pathinfo($rel, PATHINFO_FILENAME);
            $folderRel = dirname($rel);

            foreach ($sizes as $w) {
                foreach ($formats as $fmt) {
                    $outRel = ($folderRel !== '.' ? "{$folderRel}/" : '')."{$name}-{$w}.{$fmt}";
                    if (Storage::disk($disk)->exists($outRel)) continue;

                    try {
                        $img = Image::load($abs)->width($w)->quality($quality)->optimize();
                        $img->save(Storage::disk($disk)->path($outRel));
                        $this->line("→ {$outRel}");
                    } catch (Throwable $e) {
                        $this->warn("skip {$outRel}: ".$e->getMessage());
                    }
                }
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}