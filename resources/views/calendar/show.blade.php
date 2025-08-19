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
<div class="prose max-w-3xl text-slate-800 mt-10 mb-10 text-lg leading-relaxed bg-white/70 backdrop-blur-md rounded-xl shadow-xl ring-1 ring-black/5 p-6 mx-auto">
    <h1 class="text-3xl font-bold mb-2 text-slate-900">{{ $event->name }}</h1>

    @if ($event->championship)
        <p class="text-indigo-700 font-semibold mb-1">
            🏆 Part of the {{ strtoupper($event->championship) }} Championship
        </p>
    @endif

    <p class="text-slate-700 italic mb-1">📍 {{ $event->location ?? 'Location TBD' }}</p>
    <p class="text-slate-600 mb-4">
        📅 {{ optional($event->start_date)->format('F j, Y') }}
        @if ($event->end_date) – {{ $event->end_date->format('F j, Y') }} @endif
    </p>

    <section class="text-lg leading-relaxed text-slate-800">
        {!! nl2br(e($event->description ?? 'No additional information available.')) !!}
    </section>
</div>

{{-- Stage map carousel --}}
@if($stages->count())
<section class="relative mt-8 max-w-6xl mx-auto px-4" data-stage-carousel>
    <button type="button" data-embla-prev
        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white/95 hover:bg-white shadow p-2 ring-1 ring-black/5">
        ←
    </button>

    <div class="overflow-hidden rounded-2xl ring-1 ring-black/5 bg-white/60" data-embla-viewport>
        <div class="flex">
            @foreach($stages as $ss)
                <div class="min-w-0 flex-[0_0_94%] md:flex-[0_0_66%] px-4 py-5" id="ss-{{ $ss->id }}">
                    <article class="rounded-2xl bg-white shadow-lg p-4 ring-1 ring-black/5">
                        {{-- Image --}}
                        @php
                            $img = $ss->map_image_url;

                            if ($img) {
                                if (preg_match('~^https?://~', $img)) {
                                    // full URL, keep as-is
                                } elseif (str_starts_with($img, '/')) {
                                    // relative path (e.g. /images/maps/ss1.jpg)
                                    $img = asset(ltrim($img, '/'));
                                } else {
                                    // just a filename (e.g. ss1.jpg)
                                    $img = asset('images/maps/' . ltrim($img, '/'));
                                }
                            }
                        @endphp

                        @if($img)
                          <img src="{{ $img }}" alt="{{ $stageTitle($ss) }} map"
                               class="w-full rounded-xl aspect-[16/9] object-cover ring-1 ring-black/5">
                        @endif

                        {{-- Title + day chip --}}
                        <div class="mt-4 flex items-start justify-between gap-4">
                            <h3 class="text-xl md:text-2xl font-bold leading-snug text-slate-900">
                                {{ $stageTitle($ss) }}
                            </h3>
                            @php $pal = $dayPalette(optional($ss->day)->date); @endphp
                            <span class="shrink-0 px-2 py-1 text-xs font-semibold rounded-md {{ $pal['bg'] }} {{ $pal['t'] }}">
                                {{ optional($ss->day)->label ?? optional($ss->start_time_local)->format('D j M') ?? '—' }}
                            </span>
                        </div>

                        {{-- Badges --}}
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-[12px] text-slate-700">
                            @if(($ss->stage_type ?? 'SS') === 'SD')
                                <span class="px-2 py-1 rounded-full bg-slate-100 ring-1 ring-slate-200 text-slate-800 font-semibold">Shakedown</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-indigo-50 ring-1 ring-indigo-200 text-indigo-700 font-semibold">
                                    {{ 'SS ' . ($ss->ss_number ?? '?') }}@if(!empty($ss->second_ss_number))/{{ $ss->second_ss_number }}@endif
                                    @if(!empty($ss->is_super_special)) • S @endif
                                </span>
                            @endif

                            @if($ss->start_time_local)
                                <span class="px-2 py-1 rounded-full bg-emerald-50 ring-1 ring-emerald-200 text-emerald-700">
                                    Start {{ $ss->start_time_local->format('H:i') }}
                                </span>
                            @endif
                            @if($ss->second_pass_time_local)
                                <span class="px-2 py-1 rounded-full bg-amber-50 ring-1 ring-amber-200 text-amber-700">
                                    {{ $ss->second_pass_time_local->format('H:i') }}
                                </span>
                            @endif
                            @if(!is_null($ss->distance_km))
                                <span class="px-2 py-1 rounded-full bg-sky-50 ring-1 ring-sky-200 text-sky-700">
                                    {{ number_format($ss->distance_km, 1) }} km
                                </span>
                            @endif
                        </div>

                        {{-- Collapsible map embed --}}
                        @if($ss->map_embed_url)
                            <details class="mt-4 group">
                                <summary class="cursor-pointer select-none text-blue-700 hover:text-blue-800 font-semibold">
                                    See interactive map
                                </summary>
                                <div class="mt-3">
                                    <iframe src="{{ $ss->map_embed_url }}" loading="lazy"
                                            class="w-full h-72 rounded-xl border ring-1 ring-black/5"></iframe>
                                </div>
                            </details>
                        @endif

                        {{-- GPX --}}
                        @if(!empty($ss->gpx_path))
                            <a class="mt-3 inline-block text-red-600 font-semibold hover:underline"
                               href="{{ Storage::url($ss->gpx_path) }}">
                               Download GPX
                            </a>
                        @endif
                    </article>
                </div>
            @endforeach
        </div>
    </div>

    <button type="button" data-embla-next
        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white/95 hover:bg-white shadow p-2 ring-1 ring-black/5">
        →
    </button>
</section>
@endif

{{-- Detail per day --}}
@if($days->count())
<section class="mt-12 max-w-6xl mx-auto px-4">
  <h2 class="text-center text-3xl font-extrabold tracking-wide text-slate-900">DETAIL PER DAY</h2>

  <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach($days->sortBy('date') as $day)
      @php $pal = $dayPalette($day->date); @endphp

      <div class="rounded-2xl bg-white shadow-lg ring-1 ring-slate-200">
        <div class="flex items-center justify-between px-4 pt-4">
          <span class="text-[11px] font-semibold uppercase tracking-wide {{ $pal['t'] }} {{ $pal['bg'] }} border {{ $pal['b'] }} rounded px-2 py-1">
            {{ $day->label ?? $day->date->isoFormat('dddd D') }}
          </span>
        </div>

        <div class="mt-3 space-y-3 p-4 pt-3">
          @forelse(($byDay[$day->id] ?? collect()) as $ss)
            <a href="#ss-{{ $ss->id }}"
               class="block bg-white rounded-xl border {{ $pal['b'] }}/50 border-l-4 hover:shadow-md transition">
              <div class="px-3 py-2">
                <div class="text-sm font-semibold text-slate-900">
                  {{ $ss->name ?? 'Stage' }}
                </div>

                <div class="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-slate-700">
                  @if(($ss->stage_type ?? 'SS') === 'SD')
                    <span class="px-2 py-0.5 rounded bg-slate-50 border border-slate-200 text-slate-800 font-medium">Shakedown</span>
                  @else
                    <span class="px-2 py-0.5 rounded bg-violet-50 border border-violet-200 text-violet-800 font-medium">
                      SS {{ $ss->ss_number ?? '?' }}@if(!empty($ss->second_ss_number))/{{ $ss->second_ss_number }}@endif
                    </span>
                  @endif

                  @if($ss->start_time_local)
                    <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800">
                      {{ $ss->start_time_local->format('H:i') }}
                    </span>
                  @endif

                  @if($ss->second_pass_time_local)
                    <span class="px-2 py-0.5 rounded bg-amber-50 border border-amber-200 text-amber-800">
                      {{ $ss->second_pass_time_local->format('H:i') }}
                    </span>
                  @endif

                  @if(!is_null($ss->distance_km))
                    <span class="px-2 py-0.5 rounded bg-sky-50 border border-sky-200 text-sky-800">
                      {{ number_format($ss->distance_km,1) }} km
                    </span>
                  @endif
                </div>
              </div>
            </a>
          @empty
            <p class="text-xs text-slate-500">No stages assigned yet.</p>
          @endforelse
        </div>
      </div>
    @endforeach
  </div>
</section>
@endif

<div class="text-center mt-10 mb-12">
    <a href="{{ Route::has('calendar.index') ? route('calendar.index') : route('calendar') }}"
       class="inline-block text-blue-700 hover:text-blue-800 transition font-semibold">
        ← Back to Calendar
    </a>
</div>
@endsection