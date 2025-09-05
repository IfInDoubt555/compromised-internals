<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RallyEvent;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminRallyEventController extends Controller
{
    public function index(): View
    {
        $events = RallyEvent::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Normalize official_url (auto-prefix https:// if missing)
        if ($request->filled('official_url')) {
            $url = (string) $request->input('official_url');

            if (! (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                $request->merge(['official_url' => 'https://' . $url]);
            }
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'location'     => 'required|string|max:255',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'championship' => 'nullable|string|max:50',
            'map_embed_url'=> ['nullable','string','max:1000'],
            'official_url' => ['nullable','url','max:255'],
        ]);

        $event = new RallyEvent($validated);
        $event->slug = Str::slug($validated['name']).'-'.uniqid();
        $event->save();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(RallyEvent $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, RallyEvent $event): RedirectResponse
    {
        // Normalize official_url (auto-prefix https:// if missing)
        if ($request->filled('official_url')) {
            $url = (string) $request->input('official_url');

            if (! (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                $request->merge(['official_url' => 'https://' . $url]);
            }
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'location'     => 'required|string|max:255',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'championship' => 'nullable|string|max:50',
            'map_embed_url'=> ['nullable','string','max:1000'],
            'official_url' => ['nullable','url','max:255'],
        ]);

        $event->fill($validated)->save();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(RallyEvent $event): RedirectResponse
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted.');
    }
}