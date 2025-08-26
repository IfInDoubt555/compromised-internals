{{-- resources/views/history/bookmarks.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- before: <div x-data="{ decade: '{{ $decade }}', ... }" :class="`theme-${decade}`" class="min-h-screen"> --}}
<div id="theme-wrapper"
     x-data="{ decade: '{{ $decade }}', openYear: '{{ $year ?? '' }}' }"
     :class="{
       'decade-1960': decade==='1960s',
       'decade-1970': decade==='1970s',
       'decade-1980': decade==='1980s',
       'decade-1990': decade==='1990s',
       'decade-2000': decade==='2000s',
       'decade-2010': decade==='2010s',
       'decade-2020': decade==='2020s'
     }"
     class="min-h-screen">
  <header class="max-w-6xl mx-auto px-4 py-6 flex items-center gap-3">
    <h1 class="text-3xl font-bold">Rally History Archive</h1>
    <div class="mb-4 flex gap-2">
      @foreach(['events' => 'Events', 'cars' => 'Cars', 'drivers' => 'Drivers'] as $key => $label)
        <a href="{{ route('history.index', ['view'=>'bookmarks','decade'=>$decade,  'tab'=>$key]) }}"
           class="px-3 py-1 rounded-full border text-sm
                  {{ $tab === $key ? 'bg-white/70' : 'bg-white/30 hover:bg-white/50' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>
    <a href="{{ route('history.index', ['view' => 'timeline']) }}" class="text-sm underline">Try Timeline →</a>
  </header>

  <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-[140px_1fr] gap-6">
    {{-- Bookmarks (decade tabs) --}}
    <nav class="relative">
      <ul class="flex md:flex-col gap-2">
        @foreach($decades as $d)
          @php
            $active = $d === $decade;
            $params = ['view'=>'bookmarks','decade'=>$d,'tab'=>$tab];
            if ($tab === 'events' && $year) { $params['year'] = $year; }
          @endphp
          <li>
            <a href="{{ route('history.index', $params) }}"
               class="group relative block px-4 py-2 font-semibold
                      bg-[var(--bg)] border-l-4
                      {{ $active ? 'border-[var(--accent)] translate-x-0' : 'border-transparent     hover:translate-x-1' }}
                      rounded-r-lg shadow-sm transition">
              <span class="inline-block -skew-x-6">{{ $d }}</span>
              <span class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3
                           bg-[var(--accent)] rounded-tr-lg"></span>
            </a>
          </li>
        @endforeach
       </ul>
    </nav>

    {{-- Main panel --}}
    <section>
      {{-- Year chip row — only for Events --}}
        @if($tab === 'events' && !empty($years))
          <div class="flex flex-wrap gap-2 mb-4">
            @foreach($years as $y)
              <a href="{{ route('history.index', ['view'=>'bookmarks','decade'=>$decade, 'tab'=>'events','year'=>$y]) }}"
                 class="px-3 py-1 rounded-full border text-sm
                        {{ (string)$y === (string)($year ?? '') ? 'bg-[var(--accent)] text-white' :'hover:bg-neutral-100' }}">
                {{ $y }}
              </a>
            @endforeach
          </div>
        @endif

      {{-- Results list (no cards) --}}
      <ul class="divide-y border rounded-md overflow-hidden">
        @foreach($items as $e)
           <li class="p-3 history-card hover:bg-white/70 transition">
            <a href="{{ route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $e['id']]) }}"
               class="font-medium hover:underline">
              {{ $e['title'] }}
            </a>
            <p class="text-sm opacity-80">{{ $e['bio'] ?? '' }}</p>
          </li>
        @endforeach
      </ul>
    </section>
  </div>
</div>
@endsection