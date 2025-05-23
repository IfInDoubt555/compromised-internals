<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class ImageService
{
    /**
     * Resize + store an uploaded image.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder   // e.g. "profile_pics"
     * @param  string        $prefix   // e.g. "avatar_"
     * @param  int           $width
     * @param  int|null      $height
     * @return string|null             // path under disk('public'), e.g. "profile_pics/avatar_5f8d3e.jpg"
     */
    public static function processAndStore(
        UploadedFile $file,
        string $folder,
        string $prefix = 'img_',
        int $width = 1280,
        ?int $height = null
    ): ?string {
        try {
            // 1) load & resize
            $img = Image::make($file->getRealPath())
                ->resize($width, $height, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

            // 2) pick an extension
            $ext      = strtolower($file->getClientOriginalExtension());
            $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
            if (! in_array($ext, $allowed, true)) {
                $ext = 'jpg';
            }

            // 3) build filename + path
            $filename = uniqid($prefix) . '.' . $ext;
            $path     = "{$folder}/{$filename}";

            // 4) encode at the right format & quality
            switch ($ext) {
                case 'png':
                    $encoded = $img->encode('png');
                    break;
                case 'webp':
                    $encoded = $img->encode('webp', 90);
                    break;
                default:
                    $encoded = $img->encode('jpg', 90);
            }

            // 5) persist to public disk
            Storage::disk('public')->put($path, (string) $encoded);

            return $path;
        } catch (\Throwable $e) {
            Log::error('ImageService::processAndStore failed', [
                'message' => $e->getMessage(),
                'file'    => $file->getClientOriginalName(),
            ]);
            return null;
        }
    }
}