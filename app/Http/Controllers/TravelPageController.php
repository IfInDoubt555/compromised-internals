<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use App\Models\TravelHighlight;
use Illuminate\Contracts\View\View;

class TravelPageController extends Controller
{
    public function index(): View
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
                ->whereDate('start_date', '>=', now()->toDateString())
                ->orderBy('start_date')
                ->take(3)
                ->get(['id', 'slug', 'name', 'location', 'start_date'])
                ->map(fn ($e) => [
                    'title' => "{$e->name} — Plan Trip",
                    'url'   => route('travel.plan.event', ['rallyEvent' => $e->slug]),
                ])
                ->all();
        }

        // Tips singleton for the tips card
        $tips = TravelHighlight::tips()->first();

        return view('travel.index', compact('items', 'tips'));
    }

    public function event(RallyEvent $rallyEvent): View
    {
        $seo = [
            'title'       => "Plan Your Trip – {$rallyEvent->name}",
            'description' => "Hotels, camping, flights, and car rentals for {$rallyEvent->name}" .
                             ($rallyEvent->location ? " in {$rallyEvent->location}" : '') . '.',
            'url'         => route('travel.plan.event', $rallyEvent),
        ];

        return view('travel.event', [
            'event' => $rallyEvent,
            'seo'   => $seo,
        ]);
    }
}