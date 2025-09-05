<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class AttributionController extends Controller
{
    protected string $csvPath = 'attribution-log.csv';

    public function index(Request $request): View
    {
        $showAll = $request->query('all') === '1';
        $fullPath = storage_path('app/' . $this->csvPath);
        if (!is_file($fullPath)) {
            // Create empty CSV with headers if missing
            $defaultHeaders = [
                'Filename','Path','Width','Height','Size (KB)','MIME Type',
                'Year (Guess)','Section (Guess)','Source URL','Author','License Type','Credit String'
            ];
            $handle = fopen($fullPath, 'w');
            if ($handle !== false) {
                fputcsv($handle, $defaultHeaders);
                fclose($handle);
            }
        }

        /** @var array<int,string> $rawLines */
        $rawLines = @file($fullPath, FILE_IGNORE_NEW_LINES);
        if ($rawLines === false || count($rawLines) === 0) {
            $rawLines = [implode(',', [
                'Filename','Path','Width','Height','Size (KB)','MIME Type',
                'Year (Guess)','Section (Guess)','Source URL','Author','License Type','Credit String'
            ])];
        }

        /** @var array<int,array<int,string>> $rows */
        $rows = array_map('str_getcsv', $rawLines);

        /** @var array<int,string> $headers */
        $headers = array_map('trim', (array) array_shift($rows));

        /** @var Collection<int, array<string,string>> $entries */
        $entries = collect($rows)->map(
            /** @return array<string,string> */
            function (array $row) use ($headers): array {
                $assoc = array_combine($headers, $row) ?: [];
                // Ensure all expected keys exist (avoid undefined index)
                foreach ($headers as $h) {
                    if (!array_key_exists($h, $assoc)) {
                        $assoc[$h] = '';
                    }
                }
                /** @var array<string,string> $assoc */
                return $assoc;
            }
        );
                    $entries = $entries->filter(
                /** @param array<string,string> $entry */
                function (array $entry): bool {
                    return trim($entry['Author'] ?? '') === ''
                        || trim($entry['Source URL'] ?? '') === ''
                        || trim($entry['License Type'] ?? '') === '';
                }
            );
                    return view(
            /** @var view-string $view */
            $view = 'admin.attributions',
            [
                'entries' => $entries,
                'showAll' => $showAll,
            ]
        );
        }


    public function update(Request $request): RedirectResponse
    {
                /** @var array<int, array<string,string>> $data */
        $data = (array) $request->input('attributions', []);


        /** @var array<int,string> $headers */
        $headers = [
            'Filename', 'Path', 'Width', 'Height', 'Size (KB)', 'MIME Type',
            'Year (Guess)', 'Section (Guess)', 'Source URL', 'Author', 'License Type', 'Credit String'
        ];

        /** @var array<int, array<int,string>> $updated */
        $updated = [$headers];
        foreach ($data as $item) {
            $credit = "Photo by " . ($item['Author'] ?? '') .
                      " via " . ($item['Source URL'] ?? '') .
                      " – " . ($item['License Type'] ?? '');
            $item['Credit String'] = $credit;

            $updated[] = array_map(
                /** @return string */
                function (string $header) use ($item): string {
                    return (string) ($item[$header] ?? '');
                },
                $headers
            );
        }

        $fp = fopen(storage_path('app/' . $this->csvPath), 'w');
        if ($fp !== false) {
            foreach ($updated as $row) {
                fputcsv($fp, $row);
            }
            fclose($fp);
        }

        return redirect()->back()->with('success', 'Attributions updated.');
    }
}
