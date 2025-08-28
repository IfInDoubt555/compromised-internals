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
            $items = $manual->map(fn($h) => ['title' => $h->title, 'url' => $h->url])->all();
        } else {
            $items = RallyEvent::whereDate('start_date', '>=', now())
                ->orderBy('start_date')
                ->take(3)
                ->get()
                ->map(fn($e) => [
                    'title' => "{$e->name} — Plan Trip",
                    'url'   => route('travel.plan.event', $e), // uses slug if getRouteKeyName() set
                ])
                ->all();
        }

        return view('travel.index', compact('items'));
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