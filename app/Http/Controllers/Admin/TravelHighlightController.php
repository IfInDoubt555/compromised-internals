<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelHighlight;
use Illuminate\Http\Request;

class TravelHighlightController extends Controller
{
    public function index()
    {
        // Show all highlights in Admin (not just active ones)
        $highlights = TravelHighlight::highlights()
            ->orderBy('sort_order')
            ->get();

        // Pass tips singleton to the index view
        $tips = TravelHighlight::tips()->first();

        return view('admin.travel-highlights.index', compact('highlights', 'tips'));
    }

    public function create()
    {
        return view('admin.travel-highlights.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title'      => ['required','string','max:160'],
            'url'        => ['required','url'],
            'event_id'   => ['nullable','integer'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['required','boolean'],
        ]);

        // Ensure this row is a "highlight" (tips live as a singleton with kind='tips')
        $data['kind'] = TravelHighlight::KIND_HIGHLIGHT;

        TravelHighlight::create($data);

        return redirect()
            ->route('admin.travel-highlights.index')
            ->with('status', 'Highlight created.');
    }

    public function edit(TravelHighlight $travel_highlight)
    {
        return view('admin.travel-highlights.edit', ['h' => $travel_highlight]);
    }

    public function update(Request $r, TravelHighlight $travel_highlight)
    {
        $data = $r->validate([
            'title'      => ['required','string','max:160'],
            'url'        => ['required','url'],
            'event_id'   => ['nullable','integer'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['required','boolean'],
        ]);

        $travel_highlight->update($data);

        return redirect()
            ->route('admin.travel-highlights.index')
            ->with('status', 'Highlight updated.');
    }

    public function destroy(TravelHighlight $travel_highlight)
    {
        $travel_highlight->delete();

        return back()->with('status', 'Highlight deleted.');
    }

    public function editTips()
    {
        $tips = TravelHighlight::tips()->first();

        if (!$tips) {
            $tips = TravelHighlight::create([
                'kind'      => TravelHighlight::KIND_TIPS,
                'title'     => 'Travel Tips',
                'is_active' => true,
                'tips_md'   => "Book early for Monte-Carlo and Finland — hotels & camping fill fast.\n"
                             . "Consider car rentals for Portugal or Sardinia — many stages are remote.\n"
                             . "Check official event sites for shuttles and restricted roads.",
            ]);
        }

        return view('admin.travel-highlights.tips', compact('tips'));
    }

    public function updateTips(Request $request)
    {
        $data = $request->validate([
            'tips_md'   => ['nullable','string','max:20000'],
            'is_active' => ['required','boolean'],
        ]);

        $tips = TravelHighlight::tips()->firstOrFail();
        $tips->update($data);

        return redirect()
            ->route('admin.travel-highlights.tips.edit')
            ->with('status', 'Travel tips updated.');
    }
}