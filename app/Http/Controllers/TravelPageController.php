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
        // curated highlights (only kind=highlight)
        $manual = TravelHighlight::highlights()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get(['title','url']);

        if ($manual->isNotEmpty()) {
            $items = $manual->map(fn($h) => [
                'title' => $h->title,
                'url'   => $h->url,
            ])->all();
        } else {
            // fallback: next 3 upcoming rallies
            $items = RallyEvent::query()
                ->whereDate('start_date', '>=', now()->toDateString())
                ->orderBy('start_date')
                ->take(3)
                ->get()
                ->map(fn($e) => [
                    'title' => "{$e->name} — Plan Trip",
                    'url'   => route('travel.plan.event', $e), // ensure this route exists
                ])->all();
        }

        // (optional) tips card down the page
        $tips = TravelHighlight::tips()->first();

        return view('travel.index', compact('items','tips'));
    }

    public function event(RallyEvent $event)
    {
        // You can precompute anything the view needs here
        $seo = [
            'title'       => "Plan Your Trip – {$event->name}",
            'description' => "Hotels, camping, flights, and car rentals for {$event->name} in {$event->location}.",
            'url'         => route('travel.plan.event', $event),
        ];

        return view('travel.event', compact('event', 'seo'));
    }
}