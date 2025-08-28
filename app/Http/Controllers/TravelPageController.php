<?php

namespace App\Http\Controllers;

use App\Models\TravelHighlight;
use App\Models\RallyEvent;

class TravelPageController extends Controller
{
    public function index()
    {
        // Curated highlights (only kind=highlight)
        $manual = TravelHighlight::highlights()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get(['title', 'url']);

        if ($manual->isNotEmpty()) {
            $items = $manual->map(fn ($h) => [
                'title' => $h->title,
                'url'   => $h->url,
            ])->all();
        } else {
            // Fallback: next 3 upcoming rallies
            $items = RallyEvent::query()
                ->whereDate('start_date', '>=', today()->toDateString())
                ->orderBy('start_date')
                ->take(3)
                ->get(['id', 'name', 'location', 'start_date']) // add 'slug' here if your route binds by slug
                ->map(fn ($e) => [
                    'title' => "{$e->name} — Plan Trip",
                    'url'   => route('travel.plan.event', $e), // implicit model binding
                ])->all();
        }

        // Tips singleton for the tips card
        $tips = TravelHighlight::tips()->first();

        return view('travel.index', compact('items', 'tips'));
    }

    public function event(RallyEvent $event)
    {
        $seo = [
            'title'       => "Plan Your Trip – {$event->name}",
            'description' => "Hotels, camping, flights, and car rentals for {$event->name}" .
                             ($event->location ? " in {$event->location}" : '') . '.',
            'url'         => route('travel.plan.event', $event),
        ];

        return view('travel.event', compact('event', 'seo'));
    }
}