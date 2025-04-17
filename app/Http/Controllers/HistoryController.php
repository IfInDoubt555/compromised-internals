<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\supprt\Collection;
use Parsedown;

class HistoryController extends Controller
{
    public function index()
    {
        return view('history.history'); // loads your timeline UI
    }

    public function show($decade, $event)
    {
        $json = file_get_contents(public_path('data/rally-history.json'));
        $data = json_decode($json, true);
    
        // Look in events, drivers, and cars
        $eventData = collect($data[$decade]['events'] ?? [])
            ->merge($data[$decade]['drivers'] ?? [])
            ->merge($data[$decade]['cars'] ?? [])
            ->firstWhere('id', $event);
    
        if (!$eventData) {
            abort(404);
        }
    
        $parsedown = new Parsedown();
        $eventData['details_html'] = isset($eventData['details'])
            ? $parsedown->text($eventData['details'])
            : null;
    
        return view('history.show', [
            'event' => $eventData,
            'decade' => $decade,
        ]);
    }
}
