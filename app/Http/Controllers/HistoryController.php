<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Parsedown;

class HistoryController extends Controller
{
    public function index()
    {
        return view('history.history'); // renders UI for interactive timeline
    }

    public function show($decade, $event)
    {
        $jsonPath = public_path('data/rally-history.json');

        if (!File::exists($jsonPath)) {
            abort(500, 'History data file not found.');
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        $eventData = collect($data[$decade]['events'] ?? [])
            ->merge($data[$decade]['drivers'] ?? [])
            ->merge($data[$decade]['cars'] ?? [])
            ->firstWhere('id', $event);

        if (!$eventData) {
            abort(404);
        }

        $parsedown = new Parsedown();
        $eventData['details_html'] = !empty($eventData['details'])
            ? $parsedown->text($eventData['details'])
            : null;

        return view('history.show', [
            'event' => $eventData,
            'decade' => $decade,
        ]);
    }
}
