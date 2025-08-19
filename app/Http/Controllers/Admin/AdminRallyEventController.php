<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RallyEvent;
use Illuminate\Support\Str;

class AdminRallyEventController extends Controller
{
    public function index()
    {
        $events = RallyEvent::latest()->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'location'     => 'required|string|max:255',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'championship' => 'nullable|string|max:50',
            'map_embed_url'=> ['nullable','string','max:1000'],
        ]);

        $event = new RallyEvent($validated);
        $event->slug = Str::slug($validated['name']).'-'.uniqid();
        $event->save();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(RallyEvent $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, RallyEvent $event)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'location'     => 'required|string|max:255',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'championship' => 'nullable|string|max:50',
            'map_embed_url'=> ['nullable','string','max:1000'],
        ]);

        // Single atomic update; relies on fillable in RallyEvent (including map_embed_url & championship).
        $event->fill($validated)->save();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(RallyEvent $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted.');
    }
}