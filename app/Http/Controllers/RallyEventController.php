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
        $events = RallyEvent::query()
            ->when($request->start, function ($q) use ($request) {
                $q->whereDate('end_date', '>=', $request->start);
            })
            ->when($request->end, function ($q) use ($request) {
                $q->whereDate('start_date', '<=', $request->end);
            })
            ->get()
            ->map(fn($event) => [
                'title' => $event->name,
                'start' => \Carbon\Carbon::parse($event->start_date)->startOfDay()->toIso8601String(),
                'end' => \Carbon\Carbon::parse($event->end_date)->addDay()->startOfDay()->toIso8601String(),
                'url' => route('calendar.show', $event->slug),
            ]);

        return response()->json($events);
    }

    public function show($slug)
    {
        $event = RallyEvent::where('slug', $slug)->firstOrFail();
        return view('calendar.show', compact('event'));
    }
}
