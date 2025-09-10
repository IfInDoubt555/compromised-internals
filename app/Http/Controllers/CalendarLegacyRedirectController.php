<?php

namespace App\Http\Controllers;

use App\Models\RallyEvent;
use Illuminate\Http\RedirectResponse;

class CalendarLegacyRedirectController extends Controller
{
    // /calendar/events/{event:id}  ->  /calendar/{slug}
    public function byId(RallyEvent $event): RedirectResponse
    {
        return redirect()->route('calendar.show', $event->slug, 301);
    }

    // /calendar/events/{slug}  ->  /calendar/{slug} (301)
    public function bySlug(string $slug): RedirectResponse
    {
        return redirect()->route('calendar.show', $slug, 301);
    }
}