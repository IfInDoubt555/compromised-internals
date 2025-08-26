<?php

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarEventsController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::parse($request->query('start', now()->startOfMonth()));
        $end   = Carbon::parse($request->query('end',   now()->endOfMonth()));

        $q = RallyEvent::query()
            // any event that overlaps the requested window
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
                  ->orWhereBetween('end_date',   [$start->toDateString(), $end->toDateString()])
                  ->orWhere(function ($qq) use ($start, $end) {
                      $qq->where('start_date', '<', $start->toDateString())
                         ->where('end_date',   '>', $end->toDateString());
                  });
            })
            ->orderBy('start_date');

        // Optional filter e.g. ?champ=WRC | ERC | ARA
        if ($request->filled('champ')) {
            $q->where('championship', $request->string('champ'));
        }

        $events = $q->get()->map(function (RallyEvent $ev) {
            return [
                'id'    => $ev->id,
                'title' => $ev->name,
                'start' => $ev->start_date?->toDateString(),
                // For all-day events, DTEND is exclusive in FullCalendar:
                'end'   => $ev->end_date?->copy()->addDay()->toDateString(),
                'url'   => url("/calendar/events/{$ev->id}"),
                'extendedProps' => [
                    'championship' => $ev->championship,
                    'location'     => $ev->location,
                    'description'  => $ev->description,
                ],
                'allDay' => true,
            ];
        });

        return response()->json($events);
    }
}