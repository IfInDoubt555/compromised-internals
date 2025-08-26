<?php

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use Illuminate\Http\Request;

class RallyEventController extends Controller
{
    public function index()
    {
        // Paginate instead of loading all
        $events = RallyEvent::orderBy('start_date')->paginate(10);
        return view('calendar.index', compact('events'));
    }

    public function api(Request $request)
    {
        // Events that overlap the requested window
        $events = RallyEvent::query()
            ->when($request->start, fn ($q) =>
                $q->whereDate('end_date', '>=', $request->start)
            )
            ->when($request->end, fn ($q) =>
                $q->whereDate('start_date', '<=', $request->end)
            )
            // Optional championship filter (?champ=WRC|ERC|ARA)
            ->when($request->filled('champ'), fn ($q) =>
                $q->where('championship', $request->string('champ'))
            )
            ->orderBy('start_date')
            ->get()
            ->map(function (RallyEvent $event) {
                if (!$event->start_date || !$event->end_date) {
                    return null;
                }

                return [
                    'id'     => $event->id,
                    'title'  => $event->name ?? 'Untitled Event',
                    // Send DATE strings (all-day)
                    'start'  => $event->start_date->toDateString(),
                    // FullCalendar uses exclusive end for all-day
                    'end'    => $event->end_date->copy()->addDay()->toDateString(),
                    'allDay' => true,
                    // Use canonical slug route
                    'url'    => $event->slug ? route('calendar.show', $event->slug) : '#',

                    // Provide championship etc. for class-based styling & filters
                    'extendedProps' => [
                        'championship' => $event->championship,
                        'location'     => $event->location,
                        'description'  => $event->description,
                        // 'slug' => $event->slug, // uncomment if you ever need it in JS
                    ],

                    // Intentionally omit 'color' so CSS classes control appearance
                ];
            })
            ->filter()
            ->values();

        return response()->json($events);
    }

    public function show($slug)
    {
        $event = RallyEvent::where('slug', $slug)
            ->with([
                'days'   => fn ($q) => $q->orderBy('date'),
                'stages' => fn ($q) => $q->orderBy('ss_number'),
            ])->firstOrFail();

        $stagesByDay = $event->stages->groupBy('rally_event_day_id');

        return view('calendar.show', compact('event', 'stagesByDay'));
    }
}