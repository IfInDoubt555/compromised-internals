{{-- resources/views/calendar/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    // If the controller didn't pass it (first pass / backwards compat)
    $stages   = isset($event->stages) ? $event->stages : collect();
    $days     = isset($event->days)   ? $event->days   : collect();
    $byDay    = isset($stagesByDay)   ? $stagesByDay   : $stages->groupBy('rally_event_day_id');
@endphp

<div class="prose max-w-3xl text-gray-800 mt-10 mb-10 text-lg leading-relaxed bg-white/45 backdrop-blur-md rounded-xl shadow-xl p-6 mx-auto">
    <h1 class="text-3xl font-bold mb-2">{{ $event->name }}</h1>

    @if ($event->championship)
        <p class="text-indigo-600 font-semibold mb-1">
            🏆 Part of the {{ strtoupper($event->championship) }} Championship
        </p>
    @endif

    <p class="text-gray-700 italic mb-1">
        📍 {{ $event->location ?? 'Location TBD' }}
    </p>

    <p class="text-gray-600 mb-4">
        📅 {{ optional($event->start_date)->format('F j, Y') }}
        @if ($event->end_date) - {{ $event->end_date->format('F j, Y') }} @endif
    </p>

    <section class="text-lg leading-relaxed text-gray-800">
        {!! nl2br(e($event->description ?? 'No additional information available.')) !!}
    </section>
</div>

{{-- Stage map carousel --}}
@if($stages->count())
<section class="relative mt-8 max-w-6xl mx-auto px-4" data-stage-carousel>
    <button type="button" data-embla-prev
        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white shadow p-2">←</button>

    <div class="overflow-hidden" data-embla-viewport>
        <div class="flex">
            @foreach($stages as $ss)
                <div class="min-w-0 flex-[0_0_90%] md:flex-[0_0_60%] px-4" id="ss-{{ $ss->id }}">
                    <article class="rounded-2xl bg-white/90 dark:bg-zinc-900 shadow p-4">
                        @if($ss->map_image_url)
                            <img src="{{ $ss->map_image_url }}" alt="SS{{ $ss->ss_number }} map"
                                 class="w-full rounded-xl aspect-[16/9] object-cover">
                        @endif
                        <h3 class="mt-4 text-2xl font-bold">
                            {{ $ss->name }} (SS {{ $ss->ss_number }}{{ $ss->is_super_special ? '/S' : '' }})
                        </h3>
                        <p class="text-xs text-gray-500">
                            @if($ss->start_time_local) {{ $ss->start_time_local->format('D H:i') }} @endif
                            @if($ss->second_pass_time_local) • {{ $ss->second_pass_time_local->format('H:i') }} @endif
                            @if(!is_null($ss->distance_km)) • {{ number_format($ss->distance_km,1) }} km @endif
                        </p>

                        @if($ss->map_embed_url)
                            <div class="mt-3">
                                <iframe src="{{ $ss->map_embed_url }}" loading="lazy"
                                        class="w-full h-64 rounded-xl border"></iframe>
                            </div>
                        @endif

                        @if(!empty($ss->gpx_path))
                            <a class="mt-3 inline-block text-red-600 font-semibold"
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
        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white shadow p-2">→</button>
</section>
@endif

{{-- Detail per day --}}
@if($days->count())
<section class="mt-10 max-w-6xl mx-auto px-4">
    <h2 class="text-center text-3xl font-extrabold tracking-wide">DETAIL PER DAY</h2>
    <div class="mt-6 grid gap-6 md:grid-cols-4">
        @foreach($days as $day)
            <div class="rounded-2xl bg-white/90 dark:bg-zinc-900 shadow p-4">
                <div class="text-xs font-bold uppercase text-white bg-blue-900 rounded px-2 py-1">
                    {{ $day->label ?? strtoupper($day->date->format('l j')) }}
                </div>
                <div class="mt-3 space-y-3">
                    @forelse(($byDay[$day->id] ?? collect()) as $ss)
                        <a href="#ss-{{ $ss->id }}" class="block rounded-xl border p-3 hover:shadow transition">
                            <div class="text-sm font-semibold">
                                {{ $ss->name }} (SS {{ $ss->ss_number }}{{ $ss->is_super_special ? '/S' : '' }})
                            </div>
                            <div class="text-[11px] text-gray-500">
                                @if($ss->start_time_local) Start {{ $ss->start_time_local->format('H:i') }} @endif
                                @if($ss->second_pass_time_local) / {{ $ss->second_pass_time_local->format('H:i') }} @endif
                                @if(!is_null($ss->distance_km)) • {{ number_format($ss->distance_km,1) }} km @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-gray-500">No stages assigned yet.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

<div class="text-center mt-10 mb-10">
    {{-- Use whichever route name you have configured --}}
    <a href="{{ Route::has('calendar.index') ? route('calendar.index') : route('calendar') }}"
       class="inline-block text-blue-600 hover:text-blue-800 transition font-semibold">
        ← Back to Calendar
    </a>
</div>
@endsection