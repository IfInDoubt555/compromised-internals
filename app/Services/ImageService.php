<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

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
            $image = $manager->read($file->getContent());

            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Use original extension (lowercase) if supported
            $ext = strtolower($file->getClientOriginalExtension());
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed)) {
                $ext = 'jpg'; // fallback to jpg if not allowed
            }

            $filename = uniqid($prefix) . '.' . $ext;

            // Save image in the correct format based on extension
            switch ($ext) {
                case 'png':
                    Storage::disk('public')->put("{$folder}/{$filename}", (string) $image->toPng());
                    break;
                case 'webp':
                    Storage::disk('public')->put("{$folder}/{$filename}", (string) $image->toWebp(90));
                    break;
                case 'jpeg':
                case 'jpg':
                default:
                    Storage::disk('public')->put("{$folder}/{$filename}", (string) $image->toJpeg(90));
                    break;
            }

            return "{$folder}/{$filename}";
        } catch (\Throwable $e) {
            Log::error('Image processing failed', ['message' => $e->getMessage()]);
            return null;
        }
    }
}