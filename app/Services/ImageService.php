<?php

namespace App\Services;

use App\Jobs\GenerateImageVariants;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    public static function processAndStore(
        UploadedFile $file,
        string $folder,
        string $prefix = 'img_',
        int $width = 1280,
        ?int $height = null
    ): ?string {
        try {
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($file->getContent());

            // Intervention Image v3: preserve aspect ratio & don't upscale
            if ($height) {
                $image->scaleDown(width: $width, height: $height);
            } else {
                $image->scaleDown(width: $width);
            }

            // normalize ext
            $ext = strtolower($file->getClientOriginalExtension());
            $allowed = ['jpg','jpeg','png','webp','avif'];
            if (!in_array($ext, $allowed, true)) {
                $ext = 'jpg';
            }

            $filename = uniqid($prefix) . '.' . $ext;
            $relPath  = "{$folder}/{$filename}";

            // save original (normalized)
            switch ($ext) {
                case 'png':
                    Storage::disk('public')->put($relPath, (string) $image->toPng());
                    break;
                case 'webp':
                    Storage::disk('public')->put($relPath, (string) $image->toWebp(90));
                    break;
                case 'avif':
                    Storage::disk('public')->put($relPath, (string) $image->toAvif(60));
                    break;
                default: // jpg
                    Storage::disk('public')->put($relPath, (string) $image->toJpeg(90));
            }

            // fire-and-forget responsive variants
            GenerateImageVariants::dispatch('public', $relPath, [160,320,640], ['webp','avif'])
                ->onQueue('images');

            return $relPath;
        } catch (\Throwable $e) {
            Log::error('Image processing failed', ['message' => $e->getMessage()]);
            return null;
        }
    }
}