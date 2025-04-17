<?php

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use Illuminate\Http\Request;

class RallyEventController extends Controller
{
    public function index()
    {
        $events = \App\Models\RallyEvent::orderBy('start_date')->get();

        return view('calendar.index', compact('events'));
    }

    public function api()
    {
        $events = RallyEvent::all()->map(function ($event) {
            return [
                'title' => $event->name,
                'start' => $event->start_date,
                'end' => $event->end_date,
                'url' => route('calendar.show', $event->slug),
            ];
        });

        return response()->json($events);
    }

    public function show($slug)
    {
        $event = RallyEvent::where('slug', $slug)->firstOrFail();
        return view('calendar.show', compact('event'));
    }
}
