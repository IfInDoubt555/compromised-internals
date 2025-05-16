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
        try {
            $events = RallyEvent::query()
                ->when($request->start, function ($q) use ($request) {
                    $q->whereDate('end_date', '>=', $request->start);
                })
                ->when($request->end, function ($q) use ($request) {
                    $q->whereDate('start_date', '<=', $request->end);
                })
                ->get()
                ->map(function ($event) {
                    $color = match ($event->championship) {
                        'WRC' => '#1E40AF',
                        'ARA' => '#15803D',
                        'ERC' => '#9333EA',
                        default => '#6B7280',
                    };

                    return [
                        'title' => $event->name,
                        'start' => \Carbon\Carbon::parse($event->start_date)->startOfDay()->toIso8601String(),
                        'end' => \Carbon\Carbon::parse($event->end_date)->addDay()->startOfDay()->toIso8601String(),
                        'url' => route('calendar.show', $event->slug),
                        'color' => $color,
                    ];
                });

            return response()->json($events);
        } catch (\Throwable $e) {
            Log::error('Error loading calendar events: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'start' => $request->start,
                'end' => $request->end,
            ]);

            return response()->json(['error' => 'Failed to load events.'], 500);
        }
    }

    public function show($slug)
    {
        $event = RallyEvent::where('slug', $slug)->firstOrFail();
        return view('calendar.show', compact('event'));
    }
}