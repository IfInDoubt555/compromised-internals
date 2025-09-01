<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class ImageVariantService
{
    /**
     * Generate responsive variants for a single image.
     * Returns number of variants written. Idempotent (skips existing files).
     *
     * @param  string $disk     e.g. 'public'
     * @param  string $path     e.g. 'posts/post_68b43e6739f86.png'
     * @param  int[]  $sizes    e.g. [160,320,640]
     * @param  string[] $formats e.g. ['webp','avif']
     */
    public function generateVariants(string $disk, string $path, array $sizes, array $formats): int
    {
        $disk = $disk ?: 'public';
        if (!Storage::disk($disk)->exists($path)) {
            return 0;
        }

        // read original into temp file
        $tmpIn = tempnam(sys_get_temp_dir(), 'img-in');
        file_put_contents($tmpIn, Storage::disk($disk)->get($path));

        $dir  = Str::beforeLast($path, '/');
        $base = Str::of($path)->afterLast('/')->beforeLast('.');

        $written = 0;

        foreach ($formats as $fmt) {
            foreach ($sizes as $w) {
                $targetRel = "{$dir}/{$base}-{$w}.{$fmt}";
                if (Storage::disk($disk)->exists($targetRel)) {
                    continue; // idempotent
                }

                $tmpOut = tempnam(sys_get_temp_dir(), 'img-out').'.'.$fmt;

                // Spatie\Image uses Imagick if available; optimize() will call the system optimizers.
                Image::load($tmpIn)
                    ->format($fmt)
                    ->width($w)
                    ->optimize()
                    ->save($tmpOut);

                Storage::disk($disk)->put($targetRel, file_get_contents($tmpOut));
                @unlink($tmpOut);

                $written++;
            }
        }

        @unlink($tmpIn);

        return $written;
    }
}