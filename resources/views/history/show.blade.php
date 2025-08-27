{{-- resources/views/history/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  $tab    = request()->route('tab') ?? 'events';
  $decade = request()->route('decade');

  $decadeMap = [
    '1960s' => 'decade-1960',
    '1970s' => 'decade-1970',
    '1980s' => 'decade-1980',
    '1990s' => 'decade-1990',
    '2000s' => 'decade-2000',
    '2010s' => 'decade-2010',
    '2020s' => 'decade-2020',
  ];
  $decadeClass = $decadeMap[$decade] ?? 'decade-1960';

  $label = match ($tab) {
    'drivers' => 'Driver',
    'cars'    => 'Car',
    default   => 'Event',
  };

  // Optional fields
  $imageUrl       = $item['image_url']       ?? null;
  $dateStr        = $item['date']            ?? null;
  $startLocation  = $item['start_location']  ?? null;
  $finishLocation = $item['finish_location'] ?? null;
  $distanceTotal  = $item['total_distance']  ?? null;
  $distanceStage  = $item['stage_distance']  ?? null;
  $surface        = $item['surface']         ?? null;
  $countries      = $item['countries']       ?? null;
  $funFact        = $item['fun_fact']        ?? null;
@endphp

<div id="theme-wrapper" class="{{ $decadeClass }}">

  {{-- ====================== HERO ====================== --}}
  <section class="relative">
    <div class="relative overflow-hidden">
      <div class="absolute inset-0">
        @if($imageUrl)
          <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-cover opacity-60">
        @else
          <div class="h-full w-full bg-gradient-to-br from-stone-800 via-stone-700 to-amber-800 opacity-80"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/30 to-black/60"></div>
      </div>

      <div class="relative max-w-5xl mx-auto px-4 pt-14 pb-16 text-white">
        <div class="inline-flex items-center gap-2 text-xs uppercase tracking-widest opacity-90">
          <span class="px-2 py-0.5 rounded bg-white/10 ring-1 ring-white/15">History</span>
          @if($countries)
            <span class="px-2 py-0.5 rounded bg-white/10 ring-1 ring-white/15">{{ $countries }}</span>
          @endif
          @if($decade)
            <span class="px-2 py-0.5 rounded bg-white/10 ring-1 ring-white/15">{{ $decade }}</span>
          @endif
        </div>

        <h1 class="mt-3 text-3xl sm:text-4xl md:text-5xl font-extrabold drop-shadow">
          {{ $item['title'] ?? $item['name'] ?? 'Untitled' }}
        </h1>

        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-white/90">
          @if($dateStr)
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a3 3 0 0 1 3 3v11a3 3 0 0 1-3 3H4a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h1V3a1 1 0 0 1 2 0v1Zm13 6H4v10a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8Z"/></svg>
              {{ $dateStr }}
            </span>
          @endif
          @if($startLocation)
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">🏁 Start: {{ $startLocation }}</span>
          @endif
          @if($finishLocation)
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">🏁 Finish: {{ $finishLocation }}</span>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- ====================== QUICK FACTS ====================== --}}
  @if($distanceTotal || $distanceStage || $surface)
    <section class="max-w-5xl mx-auto px-4 -mt-10">
      <div class="grid sm:grid-cols-3 gap-3">
        @if($distanceTotal)
          <div class="rounded-xl bg-white/80 backdrop-blur-md shadow ring-1 ring-black/5 px-4 py-3">
            <div class="text-xs uppercase tracking-wide text-stone-500">Total Distance</div>
            <div class="text-lg font-semibold">{{ $distanceTotal }}</div>
          </div>
        @endif
        @if($distanceStage)
          <div class="rounded-xl bg-white/80 backdrop-blur-md shadow ring-1 ring-black/5 px-4 py-3">
            <div class="text-xs uppercase tracking-wide text-stone-500">Stage Distance</div>
            <div class="text-lg font-semibold">{{ $distanceStage }}</div>
          </div>
        @endif
        @if($surface)
          <div class="rounded-xl bg-white/80 backdrop-blur-md shadow ring-1 ring-black/5 px-4 py-3">
            <div class="text-xs uppercase tracking-wide text-stone-500">Surface</div>
            <div class="text-lg font-semibold">{{ $surface }}</div>
          </div>
        @endif
      </div>
    </section>
  @endif

  {{-- ====================== JUMP BAR (tab-aware) ====================== --}}
  <section class="max-w-5xl mx-auto px-4">
    <div class="mt-6 flex flex-wrap gap-2">
      @if($tab === 'events')
        <a href="#overview"   class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🏁 Overview</a>
        <a href="#route"      class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🗺️ Route</a>
        <a href="#results"    class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🏆 Results</a>
        <a href="#vehicles"   class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🚗 Cars</a>
        <a href="#challenges" class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🧭 Challenges</a>
      @elseif($tab === 'drivers')
        <a href="#overview"      class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🏁 Overview</a>
        <a href="#achievements"  class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🏆 Achievements</a>
        <a href="#vehicles"      class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🚗 Vehicles</a>
        <a href="#style"         class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🧠 Style & Legacy</a>
        <a href="#teamwork"      class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🤝 Teamwork</a>
      @else {{-- cars --}}
        <a href="#overview"     class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🏁 Overview</a>
        <a href="#specs"        class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">⚙️ Specs</a>
        <a href="#highlights"   class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🏆 Highlights</a>
        <a href="#character"    class="px-3 py-1 text-sm rounded-full bg-stone-800 text-stone-100 hover:bg-stone-900">🧠 Characteristics</a>
      @endif
    </div>
  </section>

@php
  // ====================== SECTION EXTRACTION ======================
  $rawHtml = $item['details_html'] ?? $item['description'] ?? null;

  // Initialize all section vars to avoid "undefined variable" notices.
  $secOverview = $secRoute = $secResults = $secVehicles = $secChallenges = null;
  $secAchieve = $secStyle = $secTeamwork = null;
  $secSpecs = $secHighlights = $secChar = null;

  // Extract content after a specific <h2> until the next <h2>
  $extract = function (string $h2Text) use ($rawHtml) {
    if (!$rawHtml) return null;
    $pattern = '/<h2[^>]*>\s*' . preg_quote($h2Text, '/') . '\s*<\/h2>(.*?)(?=<h2|\z)/is';
    return preg_match($pattern, $rawHtml, $m) ? trim($m[1]) : null;
  };

  // Headings per tab
  if ($tab === 'events') {
    $secOverview   = $extract('🏁 Overview');
    $secRoute      = $extract('🗺️ Route Details');
    $secResults    = $extract('🏆 Results');
    $secVehicles   = $extract('🚗 Vehicle Highlights');
    $secChallenges = $extract('🧭 Navigation and Challenges');
  } elseif ($tab === 'drivers') {
    $secOverview = $extract('🏁 Overview');
    $secAchieve  = $extract('🏆 Major Achievements:') ?: $extract('🏆 Major Achievements');
    $secVehicles = $extract('🚗 Vehicle Highlights');
    $secStyle    = $extract('🧠 Driving Style and Legacy:') ?: $extract('🧠 Driving Style and Legacy');
    $secTeamwork = $extract('🧭 Navigation and Teamwork:')   ?: $extract('🧭 Navigation and Teamwork');
  } else { // cars
    $secOverview  = $extract('🏁 Overview');
    $secSpecs     = $extract('⚙️ Technical Specs & Innovations');
    $secHighlights= $extract('🏆 Competitive Highlights');
    $secChar      = $extract('🧠 Driving Characteristics');
  }

  // Convert <li> lists to "chips" and remove the <ul> from prose to avoid duplication
  $pullListAndStrip = function (?string $html) {
    if (!$html) return [[], null];
    $items = [];
    if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $html, $m)) {
      $items = array_map(fn($s) => trim($s), $m[1]);
    }
    $prose = preg_replace('/<ul[^>]*>.*?<\/ul>/is', '', $html);
    return [$items, $prose];
  };

  // Events: Route chips + prose
  $routeLis = [];
  $secRouteProse = $secRoute;
  if ($secRoute) {
    [ $routeLis, $secRouteProse ] = $pullListAndStrip($secRoute);
  }

  // Drivers: Achievements chips + prose
  $achLis = [];
  $secAchieveProse = $secAchieve;
  if ($secAchieve) {
    [ $achLis, $secAchieveProse ] = $pullListAndStrip($secAchieve);
  }

  // Cars: Specs chips + prose
  $specLis = [];
  $secSpecsProse = $secSpecs;
  if ($secSpecs) {
    [ $specLis, $secSpecsProse ] = $pullListAndStrip($secSpecs);
  }
@endphp
{{-- ====================== CONTENT SECTIONS ====================== --}}

{{-- Overview --}}
<section class="max-w-5xl mx-auto px-4 mt-6">
  <div class="space-y-6">

@if($secOverview)
  <article id="overview" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
    <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
      <div class="h-5 w-1.5 rounded-full bg-amber-400"></div>
      <h2 class="text-lg font-semibold">🏁 Overview</h2>
    </header>
    <div class="bg-white/90 backdrop-blur px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">
      {!! $secOverview !!}
    </div>
  </article>
@endif

{{-- Events: Route --}}
@if($tab === 'events' && $secRoute)
  <section id="route" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
    <header class="flex items-center gap-3 bg-gradient-to-r from-stone-900 to-stone-800 text-stone-100 px-5 py-2.5">
      <div class="h-5 w-1.5 rounded-full bg-sky-400"></div>
      <h2 class="text-lg font-semibold">🗺️ Route</h2>
    </header>
    <div class="bg-white/95 p-5 space-y-4">
      @if(count($routeLis))
        <div class="flex flex-wrap gap-2.5">
          @foreach($routeLis as $li)
            <span class="inline-flex items-center rounded-full border border-stone-300 bg-stone-50 px-2.5 py-1 text-xs sm:text-sm">{!! $li !!}</span>
          @endforeach
        </div>
      @endif
      @if($secRouteProse && trim(strip_tags($secRouteProse)) !== '')
        <div class="prose max-w-none">{!! $secRouteProse !!}</div>
      @endif
    </div>
  </section>
@endif

{{-- Events: Results --}}
@if($tab === 'events' && ($winner || $second || $third || $secResults))
  <section id="results" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
    <header class="flex items-center gap-3 bg-gradient-to-r from-amber-500 to-amber-600 text-amber-950 px-5 py-2.5">
      <div class="h-5 w-1.5 rounded-full bg-white/80"></div>
      <h2 class="text-lg font-semibold">🏆 Results</h2>
    </header>

    <div class="bg-white p-5">
      <div class="grid gap-3 sm:grid-cols-3 auto-rows-fr items-stretch">
        {{-- Winner --}}
        <div class="h-full rounded-xl border p-3 sm:p-3 flex flex-col justify-between bg-amber-50/70 border-amber-200">
          <div class="text-[11px] sm:text-xs uppercase tracking-wide opacity-80 text-amber-800/90">Overall Winner</div>
          <div class="font-semibold mt-1 text-sm sm:text-[0.95rem] leading-snug text-amber-900">{!! $winner ?? '—' !!}</div>
        </div>

        {{-- Second --}}
        <div class="h-full rounded-xl border p-3 sm:p-3 flex flex-col justify-between bg-stone-100 border-stone-200">
          <div class="text-[11px] sm:text-xs uppercase tracking-wide opacity-80 text-stone-600/90">2nd Place</div>
          <div class="font-semibold mt-1 text-sm sm:text-[0.95rem] leading-snug">{!! $second ?? '—' !!}</div>
        </div>

        {{-- Third --}}
        <div class="h-full rounded-xl border p-3 sm:p-3 flex flex-col justify-between bg-stone-100 border-stone-200">
          <div class="text-[11px] sm:text-xs uppercase tracking-wide opacity-80 text-stone-600/90">3rd Place</div>
          <div class="font-semibold mt-1 text-sm sm:text-[0.95rem] leading-snug">{!! $third ?? '—' !!}</div>
        </div>
      </div>

      @if($resultsNarrative && trim(strip_tags($resultsNarrative)) !== '')
        <div class="prose prose-stone prose-sm leading-relaxed max-w-none mt-4">{!! $resultsNarrative !!}</div>
      @endif
    </div>
  </section>
@endif

{{-- Drivers sections --}}
@if($tab === 'drivers')
  @if($secAchieve)
    <section id="achievements" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
      <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
        <div class="h-5 w-1.5 rounded-full bg-amber-400"></div>
        <h2 class="text-lg font-semibold">🏆 Major Achievements</h2>
      </header>
      <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secAchieveProse !!}</div>
    </section>
  @endif
  @if($secVehicles)
    <section id="vehicles" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
      <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
        <div class="h-5 w-1.5 rounded-full bg-pink-400"></div>
        <h2 class="text-lg font-semibold">🚗 Vehicle Highlights</h2>
      </header>
      <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secVehicles !!}</div>
    </section>
  @endif
  @if($secStyle)
    <section id="style" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
      <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
        <div class="h-5 w-1.5 rounded-full bg-blue-400"></div>
        <h2 class="text-lg font-semibold">🧠 Driving Style & Legacy</h2>
      </header>
      <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secStyle !!}</div>
    </section>
  @endif
  @if($secTeamwork)
    <section id="teamwork" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
      <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
        <div class="h-5 w-1.5 rounded-full bg-green-400"></div>
        <h2 class="text-lg font-semibold">🧭 Navigation & Teamwork</h2>
      </header>
      <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secTeamwork !!}</div>
    </section>
  @endif
@endif

{{-- Cars sections --}}
@if($tab === 'cars')
  @if($secSpecs)
    <section id="specs" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
      <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
        <div class="h-5 w-1.5 rounded-full bg-cyan-400"></div>
        <h2 class="text-lg font-semibold">⚙️ Technical Specs & Innovations</h2>
      </header>
      <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secSpecsProse !!}</div>
    </section>
  @endif
  @if($secHighlights)
    <section id="highlights" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
      <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
        <div class="h-5 w-1.5 rounded-full bg-yellow-400"></div>
        <h2 class="text-lg font-semibold">🏆 Competitive Highlights</h2>
      </header>
      <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secHighlights !!}</div>
    </section>
  @endif
  @if($secChar)
    <section id="character" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
      <header class="flex items-center gap-3 bg-stone-900 text-stone-100 px-5 py-3">
        <div class="h-5 w-1.5 rounded-full bg-purple-400"></div>
        <h2 class="text-lg font-semibold">🧠 Driving Characteristics</h2>
      </header>
      <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secChar !!}</div>
    </section>
  @endif
@endif

{{-- Events: Challenges --}}
@if($tab === 'events' && $secChallenges)
  <section id="challenges" class="overflow-hidden rounded-2xl shadow ring-1 ring-black/5">
    <header class="flex items-center gap-3 bg-gradient-to-r from-stone-900 to-stone-800 text-stone-100 px-5 py-2.5">
      <div class="h-5 w-1.5 rounded-full bg-orange-400"></div>
      <h2 class="text-lg font-semibold">🧭 Navigation & Challenges</h2>
    </header>
    <div class="bg-white/95 px-5 py-4 prose prose-stone prose-sm max-w-none leading-relaxed">{!! $secChallenges !!}</div>
  </section>
@endif
  </div>
</section>

{{-- ====================== FUN FACT (optional) ====================== --}}
@if($funFact)
  <section class="max-w-5xl mx-auto px-4">
    <div class="mt-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 p-5 shadow">
      <div class="flex items-start gap-3">
        <span class="text-2xl">💡</span>
        <div>
          <h3 class="font-semibold">Fun Fact</h3>
          <p class="mt-1">{{ $funFact }}</p>
        </div>
      </div>
    </div>
  </section>
@endif

{{-- ====================== FOOTER NAV ====================== --}}
<section class="max-w-5xl mx-auto px-4">
  <div class="mt-8 pb-8 flex flex-col sm:flex-row items-stretch gap-4">
    {{-- Prev --}}
    <div class="flex-1 min-w-0">
      <a href="{{ !empty($previousItem)
                  ? route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $previousItem['id']])
                  : 'javascript:void(0)' }}"
         class="block rounded-xl bg-white/80 backdrop-blur-md shadow ring-1 ring-black/5 hover:bg-white/70 transition h-full">
        <div class="flex items-center justify-between gap-3 h-full px-5 py-4 min-h-[64px]">
          <span class="text-stone-700 font-medium whitespace-nowrap">← Previous {{ $label }}</span>
          <span class="text-stone-500 text-sm truncate">{{ $previousItem['title'] ?? $previousItem['name'] ?? '—' }}</span>
        </div>
      </a>
    </div>

    {{-- Back --}}
    <div class="grow-0 shrink-0">
      <a href="{{ route('history.index', ['decade' => $decade, 'tab' => $tab]) }}"
         class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-stone-900 text-stone-100 shadow ring-1 ring-black/5 hover:bg-stone-800 transition min-h-[64px]">
        Back<br class="hidden sm:block"> to {{ ucfirst($tab) }} Index
      </a>
    </div>

    {{-- Next --}}
    <div class="flex-1 min-w-0">
      <a href="{{ !empty($nextItem)
                  ? route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $nextItem['id']])
                  : 'javascript:void(0)' }}"
         class="block rounded-xl bg-white/80 backdrop-blur-md shadow ring-1 ring-black/5 hover:bg-white/70 transition h-full">
        <div class="flex items-center justify-between gap-3 h-full px-5 py-4 min-h-[64px]">
          <span class="text-stone-500 text-sm truncate">{{ $nextItem['title'] ?? $nextItem['name'] ?? '—' }}</span>
          <span class="text-stone-700 font-medium whitespace-nowrap">Next {{ $label }} →</span>
        </div>
      </a>
    </div>
  </div>
</section>

</div>
@endsection