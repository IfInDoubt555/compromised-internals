<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScanImageAttributions extends Command
{
    protected $signature = 'images:scan-attribution';
    protected $description = 'Scan image folder for metadata and prepare attribution tracking';

    public function handle()
    {
        $imageDir = public_path('images/history');
        $outputFile = storage_path('app/attribution-log.csv');

        if (!File::exists($imageDir)) {
            $this->error("Directory not found: $imageDir");
            return;
        }

        $images = File::allFiles($imageDir);
        $rows = [[
            'Filename', 'Path', 'Width', 'Height', 'Size (KB)', 'MIME Type',
            'Year (Guess)', 'Section (Guess)', 'Source URL', 'Author', 'License Type', 'Credit String'
        ]];

        foreach ($images as $image) {
            $path = $image->getRealPath();
            $size = round($image->getSize() / 1024, 2);
            $filename = $image->getFilename();
            $relativePath = str_replace(public_path(), '', $path);
            
            [$width, $height, $type] = getimagesize($path) ?: [null, null, null];
            $mime = image_type_to_mime_type($type);

            // Guess year from filename (e.g., 1986-audi.jpg)
            preg_match('/(19|20)\\d{2}/', $filename, $matches);
            $year = $matches[0] ?? '';

            // Guess section (e.g., /events/, /cars/, /drivers/)
            $section = str_contains($relativePath, '/events/') ? 'events'
                     : (str_contains($relativePath, '/cars/') ? 'cars'
                     : (str_contains($relativePath, '/drivers/') ? 'drivers' : ''));

            $rows[] = [
                $filename,
                $relativePath,
                $width,
                $height,
                $size,
                $mime,
                $year,
                $section,
                '', // Source URL (manual)
                '', // Author (manual)
                '', // License Type (manual)
                '', // Credit String (optional render)
            ];
        }

        $handle = fopen($outputFile, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        $this->info("Image scan complete. CSV saved to: {$outputFile}");
    }
}
