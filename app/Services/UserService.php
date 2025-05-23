<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Log;

class ImageService
{
    /**
     * Resize + store an uploaded image.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder       // e.g. "profile_pics"
     * @param  string        $prefix       // e.g. "avatar_"
     * @param  int           $width
     * @param  int|null      $height
     * @return string|null                 // path relative to disk root, e.g. "profile_pics/avatar_5f8d3e.jpg"
     */
    public static function processAndStore(
        UploadedFile $file,
        string $folder,
        string $prefix = 'img_',
        int $width = 1280,
        ?int $height = null
    ): ?string {
        try {
            // 1) Build the Intervention manager
            $manager = new ImageManager(['driver' => 'gd']);

            // 2) Load from real path
            $image = $manager->make($file->getRealPath());

            // 3) Resize (maintain aspect ratio + prevent upsize)
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // 4) Determine extension + mime
            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $ext = 'jpg';
            }
            $mime = match ($ext) {
                'png'   => 'image/png',
                'webp'  => 'image/webp',
                default => 'image/jpeg',
            };

            // 5) Build filename + path
            $filename = uniqid($prefix) . '.' . $ext;
            $path     = "{$folder}/{$filename}";

            // 6) Encode + store in one go
            $encoded = $image->encode($ext, 90)->getEncoded();
            Storage::disk('public')->put($path, $encoded);

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