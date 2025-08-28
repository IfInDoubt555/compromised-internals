<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelHighlight;
use Illuminate\Http\Request;

class TravelHighlightController extends Controller
{
    public function index()
    {
        // Show all highlights (active + inactive)
        $highlights = TravelHighlight::highlights()
            ->orderBy('sort_order')
            ->get();

        $tips = TravelHighlight::tips()->first();

        return view('admin.travel-highlights.index', compact('highlights', 'tips'));
    }

    public function create()
    {
        return view('admin.travel-highlights.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => ['required', 'string', 'max:160'],
            'url'        => ['required', 'url'],
            'event_id'   => ['nullable', 'integer', 'exists:rally_events,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['required', 'boolean'],
        ]);

        $data['kind']       = TravelHighlight::KIND_HIGHLIGHT;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        TravelHighlight::create($data);

        return redirect()
            ->route('admin.travel-highlights.index')
            ->with('status', 'Highlight created.');
    }

    public function edit(TravelHighlight $travel_highlight)
    {
        // Don’t allow editing the tips record through highlight routes
        abort_if($travel_highlight->kind !== TravelHighlight::KIND_HIGHLIGHT, 404);

        return view('admin.travel-highlights.edit', ['h' => $travel_highlight]);
    }

    public function update(Request $request, TravelHighlight $travel_highlight)
    {
        abort_if($travel_highlight->kind !== TravelHighlight::KIND_HIGHLIGHT, 404);

        $data = $request->validate([
            'title'      => ['required', 'string', 'max:160'],
            'url'        => ['required', 'url'],
            'event_id'   => ['nullable', 'integer', 'exists:rally_events,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['required', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $travel_highlight->update($data);

        return redirect()
            ->route('admin.travel-highlights.index')
            ->with('status', 'Highlight updated.');
    }

    public function destroy(TravelHighlight $travel_highlight)
    {
        abort_if($travel_highlight->kind !== TravelHighlight::KIND_HIGHLIGHT, 404);

        $travel_highlight->delete();

        return back()->with('status', 'Highlight deleted.');
    }

    public function editTips()
    {
        $tips = TravelHighlight::tips()->first();

        if (!$tips) {
            $tips = TravelHighlight::create([
                'kind'       => TravelHighlight::KIND_TIPS,
                'title'      => 'Travel Tips',
                'is_active'  => true,
                'tips_md'    => "Book early for Monte-Carlo and Finland — hotels & camping fill fast.\n"
                              . "Consider car rentals for Portugal or Sardinia — many stages are remote.\n"
                              . "Check official event sites for shuttles and restricted roads.",
                'sort_order' => 0,
            ]);
        }

        return view('admin.travel-highlights.tips', compact('tips'));
    }

    public function updateTips(Request $request)
    {
        $tips = TravelHighlight::tips()->firstOrFail();

        $validated = $request->validate([
            'tips_md'   => ['nullable', 'string', 'max:20000'],
            'is_active' => ['required', 'boolean'],
            'enabled'   => ['sometimes', 'array'],       // optional
            'enabled.*' => ['integer', 'min:0'],
        ]);

        $tipsMd = $validated['tips_md'] ?? '';
        $lines  = collect(preg_split('/\R/', (string) $tipsMd))
                    ->map(fn ($t) => trim($t))
                    ->filter()
                    ->values();

        $maxIdx = $lines->count() ? $lines->count() - 1 : -1;

        // If enabled[] was posted, clamp to valid indices; otherwise keep existing selection.
        $submitted = array_key_exists('enabled', $validated)
            ? $validated['enabled']
            : ($tips->tips_selection ?? []);

        $selection = $maxIdx >= 0
            ? array_values(array_intersect(array_map('intval', $submitted), range(0, $maxIdx)))
            : [];

        $tips->tips_md        = $tipsMd;
        $tips->is_active      = (bool) $validated['is_active'];
        $tips->tips_selection = $selection;
        $tips->save();

        return redirect()
            ->route('admin.travel-highlights.tips.edit')
            ->with('status', 'Travel tips updated.');
    }
}