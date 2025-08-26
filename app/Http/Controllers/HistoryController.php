<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Parsedown;

class HistoryController extends Controller
{
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

        // Use decade string as-is (e.g., "1960s"); do NOT append another "s"
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

        // Determine current index for prev/next
        $currentIndex = $collection->search(fn ($e) => (int) ($e['id'] ?? 0) === (int) $id);
        $previousItem = $currentIndex !== false ? ($collection[$currentIndex - 1] ?? null) : null;
        $nextItem     = $currentIndex !== false ? ($collection[$currentIndex + 1] ?? null) : null;

        // Fallback: render markdown if details_html missing
        if (empty($item['details_html']) && !empty($item['details'])) {
            $parsedown = new Parsedown();
            $item['details_html'] = $parsedown->text($item['details']);
        }

        return view('history.show', [
            'item'         => $item,
            'tab'          => $tab,
            'decade'       => $decade,
            'previousItem' => $previousItem, // ✅ correct mapping
            'nextItem'     => $nextItem,     // ✅ correct mapping
        ]);
    }

    /**
     * Index page with experimental layouts.
     * /history?view=timeline|bookmarks&decade=1960s&year=1963&tab=events
     */
    public function index(Request $request)
    {
        $decade = $request->query('decade', '1960s');
        $year   = $request->query('year');
        $tab    = $request->query('tab', 'events');
    
        $decades = $this->allDecades();
        $items   = $this->listItems($tab, $decade, $year);
        $years   = $this->yearsFor($decade, $tab); // only used for events
    
        return view('history.bookmarks', [
            'decades' => $decades,
            'decade'  => $decade,
            'year'    => $year,
            'tab'     => $tab,
            'items'   => $items,
            'years'   => $years,
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
                $rows = $rows->filter(fn ($r) => (int)($r['year'] ?? 0) ===     (int) $year);
            }
            return $rows->values(); // no sorting → preserve JSON order
        }
    
        // --- CARS/DRIVERS: reasonable stable sort (year then display name) ---
        if ($year) {
            $rows = $rows->where('year', (int) $year);
        }
    
        return $rows->sortBy([
            fn ($row) => (int)($row['year'] ?? 0),
            fn ($row) => (string)($row['title'] ?? $row['name'] ?? $row['model'] ?? $row    ['driver'] ?? ''),
        ])->values();
    }
}