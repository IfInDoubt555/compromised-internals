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
            'item'        => $item,
            'tab'         => $tab,
            'decade'      => $decade,
            'previousItem'=> $previousItem, // ✅ correct mapping
            'nextItem'    => $nextItem,     // ✅ correct mapping
        ]);
    }

    /**
     * Index page with experimental layouts.
     * /history?view=timeline|bookmarks&decade=1960s&year=1963&tab=events
     */
    public function index(Request $request)
    {
        $view   = $request->query('view', 'timeline');  // 'timeline' | 'bookmarks'
        $decade = $request->query('decade', '1960s');   // keep as string, e.g. '1960s'
        $year   = $request->query('year');              // optional, mainly for events
        $tab    = $request->query('tab', 'events');     // 'events' | 'cars' | 'drivers'

        $decades = $this->allDecades();

        // Load items for the chosen tab/decade (and optional year for events)
        $items = $this->listItems($tab, $decade, $year);

        $blade = $view === 'bookmarks' ? 'history.bookmarks' : 'history.timeline';

        return view($blade, [
            'decades' => $decades,
            'decade'  => $decade,
            'year'    => $year,
            'tab'     => $tab,
            'items'   => $items, // use this in your list partials
            'view'    => $view,
        ]);
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

        // Only events reliably have year; guard for others
        if ($tab === 'events' && $year) {
            $rows = $rows->where('year', (int) $year);
        }

        // Stable sorting: year then title when present
        return $rows->sortBy([
            fn ($a, $b) => ($a['year'] ?? 0) <=> ($b['year'] ?? 0),
            fn ($a, $b) => strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? '')),
        ])->values();
    }
}