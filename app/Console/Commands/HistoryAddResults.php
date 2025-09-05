<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Parse each event's details_html to extract "🏆 Results" into a structured `results` object.
 * Falls back to storage/app/history/results-lookup.json when parsing isn't possible.
 */
class HistoryAddResults extends Command
{
    protected $signature = 'history:add-results 
        {--input=public/data/rally-history.json : Path to the master history JSON}
        {--output= : If set, write to a different file; otherwise overwrite input}
        {--dry-run : Print summary only, do not write}';

    protected $description = 'Populate a structured results object for every event (winner/second/third/narrative).';

    public function handle(): int
    {
        $input  = (string) $this->option('input');
        $output = (string) ($this->option('output') ?? $input);
        $dry    = (bool) $this->option('dry-run');

        if (!File::exists($input)) {
            $this->error("Input not found: {$input}");
            return self::FAILURE;
        }

        $json = json_decode(File::get($input), true);
        if (!is_array($json)) {
            $this->error("Failed to decode JSON: {$input}");
            return self::FAILURE;
        }

        // Optional lookup file for tricky entries
        $lookupPath = storage_path('app/history/results-lookup.json');
        /** @var array{by_id: array<string, mixed>, by_title: array<string, mixed>} $lookup */
        $lookup = [
            'by_id' => [],
            'by_title' => [],
        ];
        if (File::exists($lookupPath)) {
            $tmp = json_decode(File::get($lookupPath), true);
            if (is_array($tmp)) {
                $lookup['by_id']    = $tmp['by_id']    ?? [];
                $lookup['by_title'] = $tmp['by_title'] ?? [];
            }
        }

        $updatedCount  = 0;
        $skippedCount  = 0;
        $parsedCount   = 0;
        $fallbackCount = 0;

        // The top-level shape can be: { "1960": { "events": [...] }, "1961": {...}, ... }
        foreach ($json as $yearKey => &$yearBlock) {
            if (!isset($yearBlock['events']) || !is_array($yearBlock['events'])) {
                continue;
            }

            foreach ($yearBlock['events'] as &$event) {
                // If already has `results`, skip
                if (isset($event['results']) && is_array($event['results'])) {
                    $skippedCount++;
                    continue;
                }

                $id      = $event['id']    ?? null;
                $title   = $event['title'] ?? null;
                $details = $event['details_html'] ?? '';

                $struct = $this->parseResultsFromDetailsHtml($details);

                if (!$struct) {
                    // Try lookup by id then by title
                    $struct = $lookup['by_id'][(string) $id] ?? ($title ? ($lookup['by_title'][$title] ?? null) : null);
                    if ($struct) {
                        $fallbackCount++;
                    }
                } else {
                    $parsedCount++;
                }

                if ($struct) {
                    // Normalize to canonical schema
                    $event['results'] = [
                        'winner' => [
                            'crew'  => $struct['winner']['crew']  ?? null,
                            'car'   => $struct['winner']['car']   ?? null,
                            'time'  => $struct['winner']['time']  ?? null,
                            'notes' => $struct['winner']['notes'] ?? null,
                        ],
                        'second' => [
                            'crew'  => $struct['second']['crew']  ?? null,
                            'car'   => $struct['second']['car']   ?? null,
                            'time'  => $struct['second']['time']  ?? null,
                            'notes' => $struct['second']['notes'] ?? null,
                        ],
                        'third' => [
                            'crew'  => $struct['third']['crew']   ?? null,
                            'car'   => $struct['third']['car']    ?? null,
                            'time'  => $struct['third']['time']   ?? null,
                            'notes' => $struct['third']['notes']  ?? null,
                        ],
                        'narrative_html' => $struct['narrative_html'] ?? null,
                    ];
                    $updatedCount++;
                }
            }
        }
        unset($yearBlock); // break reference

        $this->info("Parsed from HTML: {$parsedCount}");
        $this->info("Filled from lookup: {$fallbackCount}");
        $this->info("Already had results: {$skippedCount}");
        $this->info("Newly updated: {$updatedCount}");

        if ($dry) {
            $this->comment('Dry run: not writing file.');
            return self::SUCCESS;
        }

        // Back up once before overwriting
        $backup = $output . '.bak.' . date('Ymd-His');
        if ($output === $input) {
            File::copy($input, $backup);
            $this->comment("Backup written: {$backup}");
        }

        File::put($output, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->info("Wrote updated file to: {$output}");

        return self::SUCCESS;
    }

    /**
     * Extract structured results from the standardized "🏆 Results" section inside details_html.
     *
     * @return array{
     *   winner: array{crew:?string,car:?string,time:?string,notes:?string},
     *   second: array{crew:?string,car:?string,time:?string,notes:?string},
     *   third:  array{crew:?string,car:?string,time:?string,notes:?string},
     *   narrative_html:?string
     * }|null
     */
    private function parseResultsFromDetailsHtml(?string $html): ?array
    {
        if (!$html) {
            return null;
        }

        // Find the Results block quickly
        $resultsPos = mb_stripos($html, '🏆 Results');
        if ($resultsPos === false) {
            return null;
        }

        // Slice from results header onward
        $chunk = mb_substr($html, $resultsPos);

        // Simple regex helpers (crew + car in parentheses), tolerant to &amp; etc.
        $line = function (string $label) use ($chunk): ?array {
            // Match: <p><strong>LABEL:</strong> crew (car) ...</p>
            $pattern = '/<p>\s*<strong>\s*' . preg_quote($label, '/') . '\s*:<\/strong>\s*(.*?)<\/p>/is';
            if (!preg_match($pattern, $chunk, $m)) {
                return null;
            }

            $text = strip_tags($m[1]);
            // Extract "Name & Name (Car)" — car in () at end
            $crew = null;
            $car = null;
            $time = null;
            $notes = null;

            // Car: last (...) group
            if (preg_match('/\(([^()]*)\)\s*$/u', $text, $m2)) {
                $car = trim($m2[1]);
                $crewPart = trim(preg_replace('/\([^()]*\)\s*$/u', '', $text));
            } else {
                $crewPart = trim($text);
            }

            $crew = $crewPart;

            // Try grabbing a simple mm:ss or hh:mm:ss time if present inline (rare in your format)
            if (preg_match('/\b(\d{1,2}:\d{2}(?::\d{2})?)\b/u', $text, $m3)) {
                $time = $m3[1];
            }

            // Anything after a "—" or similar could be notes; optional
            if (preg_match('/[—\-–]\s*(.+)$/u', $text, $m4)) {
                $notes = trim($m4[1]);
            }

            return compact('crew', 'car', 'time', 'notes');
        };

        $winner = $line('Overall Winner') ?? $line('Winner');
        $second = $line('2nd Place') ?? $line('Second Place');
        $third  = $line('3rd Place') ?? $line('Third Place');

        if (!$winner && !$second && !$third) {
            return null;
        }

        // Narrative: first centered paragraph AFTER the results header block
        $narrative_html = null;
        if (preg_match('/<p[^>]*class="[^"]*text-center[^"]*"[^>]*>(.*?)<\/p>/is', $chunk, $m)) {
            $narrative_html = "<p class='text-center mt-4'>" . trim($m[1]) . "</p>";
        }

        return [
            'winner' => $winner ?? ['crew' => null, 'car' => null, 'time' => null, 'notes' => null],
            'second' => $second ?? ['crew' => null, 'car' => null, 'time' => null, 'notes' => null],
            'third'  => $third  ?? ['crew' => null, 'car' => null, 'time' => null, 'notes' => null],
            'narrative_html' => $narrative_html,
        ];
    }
}