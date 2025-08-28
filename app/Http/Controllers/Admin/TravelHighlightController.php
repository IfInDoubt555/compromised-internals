<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelHighlight;
use Illuminate\Http\Request;

class TravelHighlightController extends Controller
{
    public function index() {
        $highlights = TravelHighlight::orderBy('sort_order')->get();
        return view('admin.travel-highlights.index', compact('highlights'));
    }

    public function create() { return view('admin.travel-highlights.create'); }

    public function store(Request $r) {
        $data = $r->validate([
            'title'      => ['required','string','max:160'],
            'url'        => ['required','url'],
            'event_id'   => ['nullable','integer'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['required','boolean'],
        ]);
        TravelHighlight::create($data);
        return redirect()->route('admin.travel-highlights.index')->with('status','Highlight created.');
    }

    public function edit(TravelHighlight $travel_highlight) {
        return view('admin.travel-highlights.edit', ['h' => $travel_highlight]);
    }

    public function update(Request $r, TravelHighlight $travel_highlight) {
        $data = $r->validate([
            'title'      => ['required','string','max:160'],
            'url'        => ['required','url'],
            'event_id'   => ['nullable','integer'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['required','boolean'],
        ]);
        $travel_highlight->update($data);
        return redirect()->route('admin.travel-highlights.index')->with('status','Highlight updated.');
    }

    public function destroy(TravelHighlight $travel_highlight) {
        $travel_highlight->delete();
        return back()->with('status','Highlight deleted.');
    }
}