{{-- resources/views/calendar/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    // Back-compat fallbacks
    $stages = $event->stages ?? collect();
    $days   = $event->days   ?? collect();
    $byDay  = ($stagesByDay ?? null) ?: $stages->groupBy('rally_event_day_id');

    // Day color palette (all seven days)
    $dayPalette = function ($carbon) {
        $key = optional($carbon)->format('D');
        return match ($key) {
            'Mon' => ['bg' => 'bg-lime-50',    'b' => 'border-lime-400',    't' => 'text-lime-800'],
            'Tue' => ['bg' => 'bg-fuchsia-50', 'b' => 'border-fuchsia-400', 't' => 'text-fuchsia-800'],
            'Wed' => ['bg' => 'bg-teal-50',    'b' => 'border-teal-400',    't' => 'text-teal-800'],
            'Thu' => ['bg' => 'bg-orange-50',  'b' => 'border-orange-400',  't' => 'text-orange-800'],
            'Fri' => ['bg' => 'bg-blue-50',    'b' => 'border-blue-400',    't' => 'text-blue-800'],
            'Sat' => ['bg' => 'bg-amber-50',   'b' => 'border-amber-500',   't' => 'text-amber-900'],
            'Sun' => ['bg' => 'bg-rose-50',    'b' => 'border-rose-400',    't' => 'text-rose-800'],
            default => ['bg' => 'bg-slate-50', 'b' => 'border-slate-400',   't' => 'text-slate-800'],
        };
    };

    // Title helper
    $stageTitle = function ($ss) {
        if (($ss->stage_type ?? 'SS') === 'SD') {
            return trim(($ss->name ?: 'Shakedown') . ' (SD)');
        }
        $nums = 'SS ' . ($ss->ss_number ?? '?');
        if (!empty($ss->second_ss_number)) $nums .= '/' . $ss->second_ss_number;
        if (!empty($ss->is_super_special)) $nums .= ' /S';
        return trim(($ss->name ?: 'Special Stage') . " ({$nums})");
    };
@endphp

{{-- Event header --}}
<div
  class="prose max-w-3xl mt-10 mb-10 text-lg leading-relaxed
         bg-white/75 dark:bg-stone-900/75 backdrop-blur-md
         rounded-xl shadow-xl ring-1 ring-stone-900/5 dark:ring-white/10
         text-slate-800 dark:text-stone-200 p-6 mx-auto">

  <h1 class="text-3xl font-bold mb-2 text-slate-900 dark:text-stone-100">
    {{ $event->name }}
  </h1>

  @if ($event->championship)
    <p class="text-indigo-700 dark:text-indigo-300 font-semibold mb-1">
      🏆 Part of the {{ strtoupper($event->championship) }} Championship
    </p>
  @endif

  <p class="text-slate-700 dark:text-stone-300 italic mb-1">
    📍 {{ $event->location ?? 'Location TBD' }}
  </p>
  <p class="text-slate-600 dark:text-stone-400 mb-4">
    📅 {{ optional($event->start_date)->format('F j, Y') }}
    @if ($event->end_date) – {{ $event->end_date->format('F j, Y') }} @endif
  </p>

  @if($event->official_url)
    <p class="mt-2">
      <a href="{{ $event->official_url }}" target="_blank" rel="noopener nofollow"
         class="inline-flex items-center gap-2 text-blue-600 dark:text-sky-300 hover:underline">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10.59 13.41a1 1 0 0 0 1.41 1.41l4.24-4.24a3 3 0 1 0-4.24-4.24l-1.06 1.06a1 1 0 1 0 1.41 1.41l1.06-1.06a1 1 0 1 1 1.41 1.41l-4.24 4.24ZM13.41 10.59a1 1 0 0 0-1.41-1.41L7.76 13.41a3 3 0 1 0 4.24 4.24l1.06-1.06a1 1 0 1 0-1.41-1.41l-1.06 1.06a1 1 0 1 1-1.41-1.41l4.24-4.24Z"/>
        </svg>
        Official Website
      </a>
    </p>
  @endif

  {{-- Description (force readable color in dark) --}}
  <section class="mt-3 text-lg leading-relaxed text-slate-800 dark:text-stone-200">
    {!! nl2br(e($event->description ?? 'No additional information available.')) !!}
  </section>
</div>


{{-- Stage map carousel --}}
@if($stages->count())
<section class="relative mt-8 max-w-6xl mx-auto px-4" data-stage-carousel data-stages>
  {{-- Prev --}}
  <button type="button" data-embla-prev
          class="absolute left-2 top-1/2 -translate-y-1/2 z-10 rounded-full
                 bg-white/95 hover:bg-white shadow p-2 ring-1 ring-black/5
                 dark:bg-stone-800 dark:hover:bg-stone-700 dark:text-stone-100 dark:ring-white/10
                 focus:outline-none focus:ring-2 focus:ring-sky-400/50"
          aria-label="Previous stage">
    ←
  </button>

  <div class="overflow-hidden rounded-2xl ring-1 ring-black/5 bg-white/60
              dark:bg-stone-900/75 dark:ring-white/10 supports-[backdrop-filter]:backdrop-blur-sm"
       data-embla-viewport>
    <div class="flex">
      @foreach($stages as $ss)
        <div class="min-w-0 flex-[0_0_94%] md:flex-[0_0_66%] px-4 py-5" id="ss-{{ $ss->id }}">
          <article class="rounded-2xl bg-white dark:bg-stone-900/55 shadow-lg p-4
                          ring-1 ring-black/5 dark:ring-white/10">
            @if($ss->map_image_src)
              <img
                src="{{ $ss->map_image_src }}"
                alt="{{ $stageTitle($ss) }} map"
                loading="lazy" decoding="async"
                class="w-full rounded-xl aspect-[16/9] object-cover
                       ring-1 ring-black/5 dark:ring-white/10">
            @endif

            <div class="mt-4 flex items-start justify-between gap-4">
              <h3 class="text-xl md:text-2xl font-bold leading-snug
                         text-slate-900 dark:text-stone-100">
                {{ $stageTitle($ss) }}
              </h3>
              @php $pal = $dayPalette(optional($ss->day)->date); @endphp
              <span class="shrink-0 px-2 py-1 text-xs font-semibold rounded-md
                           {{ $pal['bg'] }} {{ $pal['t'] }}
                           ring-1 ring-black/5 dark:ring-white/10">
                {{ optional($ss->day)->label ?? optional($ss->start_time_local)->format('D j M') ?? '—' }}
              </span>
            </div>

            <div class="mt-2 flex flex-wrap items-center gap-2 text-[12px]
                        text-slate-700 dark:text-stone-300">
              @if(($ss->stage_type ?? 'SS') === 'SD')
                <span class="px-2 py-1 rounded-full bg-slate-100 ring-1 ring-slate-200 text-slate-800 font-semibold
                             dark:bg-stone-800/60 dark:ring-white/10 dark:text-stone-200">
                  Shakedown
                </span>
              @else
                <span class="px-2 py-1 rounded-full bg-indigo-50 ring-1 ring-indigo-200 text-indigo-700 font-semibold
                             dark:bg-indigo-900/30 dark:ring-indigo-400/30 dark:text-indigo-300">
                  {{ 'SS ' . ($ss->ss_number ?? '?') }}@if(!empty($ss->second_ss_number))/{{ $ss->second_ss_number }}@endif
                  @if(!empty($ss->is_super_special)) • S @endif
                </span>
              @endif

              @if($ss->start_time_local)
                <span class="px-2 py-1 rounded-full bg-emerald-50 ring-1 ring-emerald-200 text-emerald-700
                             dark:bg-emerald-900/30 dark:ring-emerald-400/30 dark:text-emerald-300">
                  Start {{ $ss->start_time_local->format('H:i') }}
                </span>
              @endif
              @if($ss->second_pass_time_local)
                <span class="px-2 py-1 rounded-full bg-amber-50 ring-1 ring-amber-200 text-amber-700
                             dark:bg-amber-900/30 dark:ring-amber-400/30 dark:text-amber-300">
                  {{ $ss->second_pass_time_local->format('H:i') }}
                </span>
              @endif
              @if(!is_null($ss->distance_km))
                <span class="px-2 py-1 rounded-full bg-sky-50 ring-1 ring-sky-200 text-sky-700
                             dark:bg-sky-900/30 dark:ring-sky-400/30 dark:text-sky-300">
                  {{ $ss->distance_km_formatted }} km
                </span>
              @endif
            </div>

            @if(!empty($ss->gpx_path))
              <a class="mt-3 inline-block text-red-600 dark:text-rose-300 font-semibold hover:underline"
                 href="{{ Storage::url($ss->gpx_path) }}">
                Download GPX
              </a>
            @endif
          </article>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Next --}}
  <button type="button" data-embla-next
          class="absolute right-2 top-1/2 -translate-y-1/2 z-10 rounded-full
                 bg-white/95 hover:bg-white shadow p-2 ring-1 ring-black/5
                 dark:bg-stone-800 dark:hover:bg-stone-700 dark:text-stone-100 dark:ring-white/10
                 focus:outline-none focus:ring-2 focus:ring-sky-400/50"
          aria-label="Next stage">
    →
  </button>
</section>
@endif

@if(!empty($event->map_embed_url))
  <section id="route-map" class="max-w-6xl mx-auto px-4 mt-8">
    <div class="overflow-hidden rounded-2xl ring-1 ring-black/5 bg-white/80
                dark:bg-stone-900/60 dark:ring-white/10 supports-[backdrop-filter]:backdrop-blur-sm">
      <x-map-embed :src="$event->map_embed_url" />
    </div>
  </section>
@endif

{{-- Detail per day --}}
@if($days->count())
<section class="mt-12 max-w-6xl mx-auto px-4">
  <h2 class="text-center text-3xl font-extrabold tracking-wide
             text-slate-900 dark:text-stone-100">
    DETAIL PER DAY
  </h2>

  <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach($days->sortBy('date') as $day)
      @php $pal = $dayPalette($day->date); @endphp

      <div class="self-start rounded-2xl bg-white dark:bg-stone-900/70 shadow-lg
            ring-1 ring-slate-200 dark:ring-white/10">
        <div class="flex items-center justify-between px-4 pt-4">
          <span class="text-[11px] font-semibold uppercase tracking-wide
                       {{ $pal['t'] }} {{ $pal['bg'] }} border {{ $pal['b'] }}
                       rounded px-2 py-1 ring-1 ring-black/5 dark:ring-white/10">
            {{ $day->label ?? $day->date->format('l j') }}
          </span>
        </div>

        <div class="mt-3 space-y-3 p-4 pt-3">
          @forelse(($byDay[$day->id] ?? collect()) as $ss)
            <a href="#ss-{{ $ss->id }}"
               class="block bg-white dark:bg-stone-900/50 rounded-xl
                      border {{ $pal['b'] }}/50 dark:border-white/10
                      border-l-4 hover:shadow-md transition">
              <div class="px-3 py-2">
                <div class="text-sm font-semibold text-slate-900 dark:text-stone-100">
                  {{ $ss->name ?? 'Stage' }}
                </div>

                {{-- stacked meta rows --}}
                <div class="mt-2 space-y-1 text-[11px] text-slate-700 dark:text-stone-300">

                  {{-- Stage id (SS 3 / 7 or Shakedown) --}}
                  <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center h-6 min-w-[92px] px-2 rounded
                                 bg-slate-50 border border-slate-200 text-slate-700
                                 dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
                      Stage
                    </span>

                    @if(($ss->stage_type ?? 'SS') === 'SD')
                      <span class="inline-flex items-center h-6 px-2 rounded
                                   bg-slate-50 border border-slate-200 text-slate-800 font-medium
                                   dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
                        Shakedown
                      </span>
                    @else
                      <span class="inline-flex items-center h-6 px-2 rounded
                                   bg-violet-50 border border-violet-200 text-violet-800 font-medium
                                   dark:bg-violet-900/30 dark:border-violet-400/30 dark:text-violet-300">
                        SS {{ $ss->ss_number ?? '?' }}
                        @if(!empty($ss->second_ss_number))
                          / {{ $ss->second_ss_number }}
                        @endif
                      </span>
                    @endif
                  </div>

                  {{-- 1st pass start time --}}
                  @if($ss->start_time_local)
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center justify-center h-6 min-w-[92px] px-2 rounded
                                   bg-slate-50 border border-slate-200 text-slate-700
                                   dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
                        1st pass
                      </span>
                      <span class="inline-flex items-center h-6 px-2 rounded
                                   bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium
                                   dark:bg-emerald-900/30 dark:border-emerald-400/30 dark:text-emerald-300">
                        {{ $ss->start_time_local->format('H:i') }}
                      </span>
                    </div>
                  @endif

                  {{-- 2nd pass start time (optional) --}}
                  @if($ss->second_pass_time_local)
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center justify-center h-6 min-w-[92px] px-2 rounded
                                   bg-slate-50 border border-slate-200 text-slate-700
                                   dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
                        2nd pass
                      </span>
                      <span class="inline-flex items-center h-6 px-2 rounded
                                   bg-amber-50 border border-amber-200 text-amber-800 font-medium
                                   dark:bg-amber-900/30 dark:border-amber-400/30 dark:text-amber-300">
                        {{ $ss->second_pass_time_local->format('H:i') }}
                      </span>
                    </div>
                  @endif

                  {{-- Distance --}}
                  @if(!is_null($ss->distance_km))
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center justify-center h-6 min-w-[92px] px-2 rounded
                                   bg-slate-50 border border-slate-200 text-slate-700
                                   dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
                        Distance
                      </span>
                      <span class="inline-flex items-center h-6 px-2 rounded
                                   bg-sky-50 border border-sky-200 text-sky-800 font-medium
                                   dark:bg-sky-900/30 dark:border-sky-400/30 dark:text-sky-300">
                        {{ $ss->distance_km_formatted }} km
                      </span>
                    </div>
                  @endif

                </div>
              </div>
            </a>
          @empty
            <p class="text-xs text-slate-500 dark:text-stone-400">No stages assigned yet.</p>
          @endforelse
        </div>
      </div>
    @endforeach
  </div>
</section>
@endif

<div class="text-center mt-10 mb-12">
  <a href="{{ Route::has('calendar.index') ? route('calendar.index') : route('calendar') }}"
     class="inline-block text-blue-700 hover:text-blue-800
            dark:text-sky-300 dark:hover:text-sky-200
            transition font-semibold">
    ← Back to Calendar
  </a>
</div>
@endsection