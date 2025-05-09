<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Parsedown;

class HistoryController extends Controller
{
    public function show($tab, $decade, $id)
    {
        $filePath = public_path("data/{$tab}-{$decade}s.json");

        if (!File::exists($filePath)) {
            abort(404, "Data file not found for {$tab}-{$decade}s.");
        }

        $data = json_decode(file_get_contents($filePath), true);

        if (!is_array($data)) {
            abort(500, "Failed to decode data file.");
        }

        $collection = collect($data);
        $item = $collection->firstWhere('id', (int) $id);

        if (!$item) {
            abort(404, "Item not found.");
        }

        // Add next/previous navigation
        $items = array_values($collection->all());
        $currentIndex = collect($items)->search(fn ($e) => $e['id'] == (int) $id);
        $previousItem = $items[$currentIndex - 1] ?? null;
        $nextItem = $items[$currentIndex + 1] ?? null;

        // Parse markdown only if details_html is missing
        if (empty($item['details_html']) && !empty($item['details'])) {
            $parsedown = new \Parsedown();
            $item['details_html'] = $parsedown->text($item['details']);
        }

        return view('history.show', [
            'item' => $item,
            'tab' => $tab,
            'decade' => $decade,
            'nextItem' => $nextItem,
            'previousItem' => $previousItem,
        ]);
    }

    public function index(Request $request)
    {
        $decade = $request->query('decade', 1960);
        $tab = $request->query('tab', 'events');

        return view('history.history', compact('decade', 'tab'));
    }
}
