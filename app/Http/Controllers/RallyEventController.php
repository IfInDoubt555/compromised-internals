<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use App\Services\Schema\EventSchemaBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RallyEventController extends Controller
{
    public function index(): View
    {
        // Paginate instead of loading all
        $events = RallyEvent::orderBy('start_date')->paginate(10);

        /** @var view-string $view */
        $view = 'calendar.index';
        return view($view, compact('events'));
    }

    public function api(Request $request): JsonResponse
    {
        $events = RallyEvent::query()
            // Events that overlap the requested window
            ->when($request->string('start')->isNotEmpty(), fn ($q) => $q
                ->whereDate('end_date', '>=', (string) $request->string('start')))
            ->when($request->string('end')->isNotEmpty(), fn ($q) => $q
                ->whereDate('start_date', '<=', (string) $request->string('end')))
            // Optional championship filter (?champ=WRC|ERC|ARA)
            ->when($request->filled('champ'), fn ($q) => $q
                ->where('championship', (string) $request->string('champ')))
            ->orderBy('start_date')
            ->get()
            ->map(function (RallyEvent $event): ?array {
                if ($event->start_date === null || $event->end_date === null) {
                    return null;
                }

                return [
                    'id'     => $event->getKey(),
                    'title'  => (string) ($event->name ?? 'Untitled Event'),
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

    public function show(string $slug, EventSchemaBuilder $schemaBuilder): View
    {
        $event = RallyEvent::where('slug', $slug)
            ->with([
                'days'   => static fn ($q) => $q->orderBy('date'),
                'stages' => static fn ($q) => $q->orderBy('ss_number'),
            ])
            ->firstOrFail();

        $stagesByDay = $event->stages->groupBy('rally_event_day_id');
        $schema = $schemaBuilder->build($event);

        /** @var view-string $view */
        $view = 'calendar.show';
        return view($view, compact('event', 'stagesByDay', 'schema'));
    }
}