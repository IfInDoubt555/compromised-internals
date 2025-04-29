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
        $jsonPath = public_path('data/rally-history.json');

        if (!File::exists($jsonPath)) {
            abort(500, 'History data file not found.');
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!isset($data[$decade][$tab])) {
            abort(404, 'Invalid decade or category.');
        }

        $item = collect($data[$decade][$tab])->firstWhere('id', (int) $id);

        if (!$item) {
            abort(404);
        }

        $parsedown = new Parsedown();
        $item['details_html'] = !empty($item['details'])
            ? $parsedown->text($item['details'])
            : null;

        return view('history.show', [
            'item' => $item,
            'tab' => $tab,
            'decade' => $decade,
        ]);
    }
    public function index(Request $request)
    {
        $decade = $request->query('decade', 1960);
        $tab = $request->query('tab', 'events');

        return view('history.history', compact('decade', 'tab'));
    }
}
