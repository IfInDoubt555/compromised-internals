<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Parsedown;

class HistoryController extends Controller
{
    public function show($tab, $decade, $id)
    {
        $validTabs = ['events', 'cars', 'drivers'];
        if (!in_array($tab, $validTabs)) {
            abort(404, "Invalid tab type.");
        }

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
        $currentIndex = collect($items)->search(fn($e) => $e['id'] == (int) $id);
        $previousItem = $items[$currentIndex - 1] ?? null;
        $nextItem = $items[$currentIndex + 1] ?? null;

        // Fallback: convert markdown to HTML if no details_html is provided
        if (empty($item['details_html']) && !empty($item['details'])) {
            $parsedown = new Parsedown();
            $item['details_html'] = $parsedown->text($item['details']);
        }

        return view('history.show', [
            'item' => $item,
            'tab' => $tab,
            'decade' => $decade,
            'nextItem' => $previousItem,
            'previousItem' => $nextItem,
        ]);
    }

    public function index(Request $request)
    {
        $decade = $request->query('decade', 1960);
        $tab = $request->query('tab', 'events');

        return view('history.history', compact('decade', 'tab'));
    }
}
