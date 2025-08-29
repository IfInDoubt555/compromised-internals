<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Support\SectionExtractor;
use Parsedown;

class HistoryController extends Controller
{
    /**
     * Format a single podium result row into a neat string like:
     * "Walter Schock / Rolf Moll · Mercedes-Benz 220 SE · 3h42m"
     */
    private function fmtResult(?array $r): ?string
    {
        if (!$r) return null;

        $bits = [];
        if (!empty($r['crew']))  $bits[] = e($r['crew']);
        if (!empty($r['car']))   $bits[] = e($r['car']);
        if (!empty($r['time']))  $bits[] = e($r['time']);
        if (!empty($r['notes'])) $bits[] = e($r['notes']);

        return count($bits) ? implode(' · ', $bits) : null;
    }

    /**
     * Show a single item from a tab+decade JSON file.
     * URL example: /history/events/1960s/123
     */
    public function show(string $tab, string $decade, int $id)
    {
        $validTabs = ['events', 'cars', 'drivers'];
        if (!in_array($tab, $validTabs, true)) {
            abort(404, 'Invalid tab type.');
        }

        // ✅ Normalize decade to the canonical form used by your files: "2000s"
        if (preg_match('/^\d{4}$/', $decade)) {
            // "2000" -> "2000s"
            $decade = $decade . 's';
        } elseif (!preg_match('/^\d{4}s$/', $decade)) {
            // anything else -> invalid
            abort(404, 'Invalid decade segment.');
        }

        $filePath = public_path("data/{$tab}-{$decade}.json");
        if (!File::exists($filePath)) {
            abort(404, "Data file not found for {$tab}-{$decade}.");
        }

    $filePath = public_path("data/{$tab}-{$decade}.json");
    if (!File::exists($filePath)) {
        abort(404, "Data file not found for {$tab}-{$decade}.");
    }

    $data = json_decode(File::get($filePath), true);
    if (!is_array($data)) {
        abort(500, 'Failed to decode data file.');
    }

    $collection = collect($data)->values();
    $item = $collection->firstWhere('id', (int) $id);
    if (!$item) {
        abort(404, 'Item not found.');
    }

    // Prev/next
    $currentIndex = $collection->search(fn ($e) => (int) ($e['id'] ?? 0) === (int) $id);
    $previousItem = $currentIndex !== false ? ($collection[$currentIndex - 1] ?? null) : null;
    $nextItem     = $currentIndex !== false ? ($collection[$currentIndex + 1] ?? null) : null;

    // Fallback: markdown → HTML
    if (empty($item['details_html']) && !empty($item['details'])) {
        $parsedown = new Parsedown();
        $item['details_html'] = $parsedown->text($item['details']);
    }

    // ---------- Results block (events only) ----------
    $winner = $second = $third = $resultsNarrative = null;
    $secResults = false;

    if ($tab === 'events') {
        $winnerRaw = Arr::get($item, 'results.winner');
        $secondRaw = Arr::get($item, 'results.second');
        $thirdRaw  = Arr::get($item, 'results.third');

        $winner = $this->fmtResult($winnerRaw);
        $second = $this->fmtResult($secondRaw);
        $third  = $this->fmtResult($thirdRaw);

        $resultsNarrative = Arr::get($item, 'results.narrative_html');

        $secResults = (bool) (
            $winner || $second || $third ||
            (isset($resultsNarrative) && trim(strip_tags($resultsNarrative)) !== '')
        );
    }

    // ---------- Section extraction (NEW) ----------
    $type = match ($tab) {
        'drivers' => 'drivers',
        'cars'    => 'cars',
        default   => 'events',
    };
    $sections = SectionExtractor::parse($item['details_html'] ?? null, $type); // NEW

    return view('history.show', [
        'item'             => $item,
        'tab'              => $tab,
        'decade'           => $decade,                  // "1990s"
        'themeDecade'      => (int) substr($decade, 0, 4), // 1990  ✅
        'previousItem'     => $previousItem,
        'nextItem'         => $nextItem,
        'winner'           => $winner,
        'second'           => $second,
        'third'            => $third,
        'resultsNarrative' => $resultsNarrative,
        'secResults'       => $secResults,
        'sections'         => $sections,
    ]);
}

    public function index(Request $request)
    {
        $decadeIn = $request->query('decade', '1960s');
        $tab      = $request->query('tab', 'events');
        $year     = $request->query('year');

        // ✅ Normalize decade to canonical "####s"
        if (preg_match('/^\d{4}$/', $decadeIn)) {
            $decade = $decadeIn.'s';
        } elseif (preg_match('/^\d{4}s$/', $decadeIn)) {
            $decade = $decadeIn;
        } else {
            $decade = '1960s'; // or abort(404) if you prefer strict
        }

        $decades     = $this->allDecades();
        $items       = $this->listItems($tab, $decade, $year);
        $years       = $this->yearsFor($decade, $tab);
        $themeDecade = (int) substr($decade, 0, 4);

        return view('history.bookmarks', [
            'decades'     => $decades,
            'decade'      => $decade,
            'themeDecade' => $themeDecade,
            'year'        => $year,
            'tab'         => $tab,
            'items'       => $items,
            'years'       => $years,
        ]);
    }

    /** Unique years available for a decade (per tab) */
    private function yearsFor(string $decade, string $tab = 'events'): array
    {
        return $this->listItems($tab, $decade)
            ->pluck('year')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /** Available decades (adjust or auto-detect from files if you prefer). */
    private function allDecades(): array
    {
        return ['1960s','1970s','1980s','1990s','2000s','2010s','2020s'];
    }

    /**
     * Load items for a given tab+decade from JSON and optionally filter by year.
     * Expects files like:
     *   public/data/events-1960s.json
     *   public/data/cars-1960s.json
     *   public/data/drivers-1960s.json
     */
    private function listItems(string $tab, string $decade, $year = null)
    {
        $validTabs = ['events', 'cars', 'drivers'];
        if (!in_array($tab, $validTabs, true)) {
            return collect();
        }

        $path = public_path("data/{$tab}-{$decade}.json");
        if (!File::exists($path)) {
            return collect();
        }

        $rows = collect(json_decode(File::get($path), true) ?: []);

        // --- EVENTS: keep the file's original ordering ---
        if ($tab === 'events') {
            if ($year) {
                $rows = $rows->filter(fn ($r) => (int)($r['year'] ?? 0) === (int) $year);
            }
            return $rows->values(); // no sorting → preserve JSON order
        }

        // --- CARS/DRIVERS: reasonable stable sort (year then display name) ---
        if ($year) {
            $rows = $rows->where('year', (int) $year);
        }

        return $rows->sortBy([
            fn ($row) => (int)($row['year'] ?? 0),
            fn ($row) => (string)($row['title'] ?? $row['name'] ?? $row['model'] ?? $row['driver'] ?? ''),
        ])->values();
    }
}