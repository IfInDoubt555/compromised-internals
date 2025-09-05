<?php

declare(strict_types=1);

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

    /** @var list<int> */
    public array $sizes;

    /** @var list<string> */
    public array $formats;

    /** Give optimizers some headroom */
    public int $timeout = 120;

    /** basic resiliency */
    public int $tries = 3;

    /** @var int|list<int> */
    public array|int $backoff = [5, 30, 120];

    /**
     * @param list<int>    $sizes
     * @param list<string> $formats
     */
    public function __construct(
        string $disk,
        string $path,
        array $sizes = [160, 320, 640],
        array $formats = ['webp', 'avif']
    ) {
        $this->disk    = $disk !== '' ? $disk : 'public';
        $this->path    = ltrim($path, '/'); // keep storage-relative
        $this->sizes   = $sizes;
        $this->formats = $formats;

        // Put this job on a dedicated queue without redefining $queue (avoids trait collision)
        $this->onQueue('images');
    }

    public function handle(ImageVariantService $svc): void
    {
        $written = $svc->generateVariants($this->disk, $this->path, $this->sizes, $this->formats);
        Log::info('image.variants.generated', ['path' => $this->path, 'count' => $written]);
    }

    /** @return list<string> Nice tags for Horizon */
    public function tags(): array
    {
        return ['images', 'variants', 'path:' . $this->path];
    }
}