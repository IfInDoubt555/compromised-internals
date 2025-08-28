<?php

// app/Http/Controllers/TravelPageController.php
namespace App\Http\Controllers;

use App\Models\TravelHighlight;
use App\Models\RallyEvent; // your calendar model
use Illuminate\Support\Carbon;

class TravelPageController extends Controller
{
    public function index()
{
    $manual = TravelHighlight::where('is_active', true)
        ->orderBy('sort_order')
        ->take(3)
        ->get(['title','url']);

    if ($manual->isNotEmpty()) {
        $items = $manual->map(fn($h) => [
            'title' => $h->title,
            'url'   => $h->url
        ])->all();
    } else {
        // Auto: next 3 upcoming events by start_date
        $items = RallyEvent::whereDate('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(3)
            ->get()
            ->map(fn($e) => [
                'title' => "{$e->name} — Plan Trip",
                'url'   => route('travel.plan.event', $e), // make sure you have this route
            ])
            ->all();
    }

    return view('travel.index', compact('items'));
}

    // Optional: per-event plan view
    public function event(RallyEvent $event)
    {
        return view('travel.event', compact('event'));
    }
}