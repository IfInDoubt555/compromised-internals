<?php

namespace App\Jobs;

use App\Services\ImageVariantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateImageVariants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $disk;
    public string $path;
    public array $sizes;
    public array $formats;

    /** run on a dedicated queue */
    public string $queue = 'images';
    /** give Imagick/optimizers some headroom */
    public int $timeout = 120;

    public function __construct(string $disk, string $path, array $sizes = [160,320,640], array $formats = ['webp','avif'])
    {
        $this->disk    = $disk;
        $this->path    = $path;
        $this->sizes   = $sizes;
        $this->formats = $formats;
    }

    public function handle(ImageVariantService $svc): void
    {
        $n = $svc->generateVariants($this->disk, $this->path, $this->sizes, $this->formats);
        Log::info("Image variants generated", ['path' => $this->path, 'count' => $n]);
    }
}