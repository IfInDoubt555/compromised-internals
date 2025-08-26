<?php

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $events = RallyEvent::query()
            ->when($request->start, fn($q) => $q->whereDate ('end_date', '>=', $request->start))
            ->when($request->end, fn($q) => $q->whereDate   ('start_date', '<=', $request->end))
            ->get()
            ->map(function ($event) {
                if (!$event->start_date || !$event->end_date) return null;
            
                $color = match ($event->championship) {
                    'WRC' => '#1E40AF',
                    'ARA' => '#15803D',
                    'ERC' => '#9333EA',
                    default => '#6B7280',
                };
            
                return [
                    'title'  => $event->name ?? 'Untitled Event',
                    // Send DATE strings; no time, no timezone
                    'start'  => $event->start_date->toDateString(),
                    // FullCalendar end is exclusive, so add one day
                    'end'    => $event->end_date->copy()->addDay()->toDateString(),
                    'allDay' => true,
                    'url'    => $event->slug ? route('calendar.show', $event->slug) :   '#',
                    'color'  => $color,
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
                'days'   => fn($q) => $q->orderBy('date'),
                'stages' => fn($q) => $q->orderBy('ss_number'),
            ])->firstOrFail();

        $stagesByDay = $event->stages->groupBy('rally_event_day_id');

        // keep your existing view name
        return view('calendar.show', compact('event','stagesByDay'));
    }
}