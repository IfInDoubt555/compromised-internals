<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RallyEvent;
use App\Models\RallyEventDay;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EventDayController extends Controller
{
    public function index(RallyEvent $event): View
    {
        $event->load('days');
        return view('admin.events.days', compact('event'));
    }

    // Generate days from start_date..end_date (idempotent)
    public function store(Request $request, RallyEvent $event): RedirectResponse
    {
        $request->validate(['generate' => 'nullable|boolean']); // simple trigger

        abort_unless($event->start_date && $event->end_date, 422, 'Set start/end dates first.');

        foreach (CarbonPeriod::create(
            $event->start_date->toDateString(),
            $event->end_date->toDateString()
        ) as $d) {
            $c = $d instanceof Carbon ? $d : Carbon::instance($d);
        
            RallyEventDay::firstOrCreate(
                ['rally_event_id' => $event->id, 'date' => $c->toDateString()],
                ['label' => $c->format('l j')] // or $c->isoFormat('dddd D') if you prefer
            );
        }

        return back()->with('status', 'Days generated/updated.');
    }

    public function destroy(RallyEvent $event, RallyEventDay $day): RedirectResponse
    {
        abort_if($day->rally_event_id !== $event->id, 404);
        $day->delete();
        return back()->with('status', 'Day removed.');
    }
}