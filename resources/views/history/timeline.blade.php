{{-- resources/views/history/timeline.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="timelineTheme()" x-init="init()" :class="themeClass" class="min-h-screen">
  <header class="max-w-5xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3">
      <h1 class="text-3xl font-bold">Rally History Archive</h1>
      <a href="{{ route('history.index', ['view' => 'bookmarks']) }}" class="text-sm underline">Try Bookmarks →</a>
    </div>
    <p class="text-neutral-600 mt-2">Scroll; theme changes per decade.</p>
  </header>

  <main class="max-w-5xl mx-auto px-4 pb-24">
    @foreach($decades as $d)
      <section id="d-{{ $d }}" data-decade="{{ $d }}" class="mb-10">
        <div class="sticky top-0 z-10 -mx-4 px-4 py-2 bg-[var(--bg)]/90 backdrop-blur border-b"
             x-intersect.full="onDecadeEnter('{{ $d }}')">
          <h2 class="text-xl font-bold">{{ $d }}</h2>
        </div>

        <ul class="divide-y border rounded-md overflow-hidden">
            @foreach(($eventsByDecade[$d] ?? []) as $e)
            <li class="p-3">
            <a href="{{ route('history.show', ['tab' => 'events', 'decade' => $d, 'id' => $e['id']]) }}" 
               class="font-medium hover:underline">
                {{ $e['year'] }} — {{ $e['title'] }}
            </a>
            <p class="text-sm text-neutral-600 line-clamp-2">{{ $e['bio'] ?? '' }}</p>
            </li>
          @endforeach
        </ul>

        {{-- Optional: load next decade button if you want to shorten first paint --}}
        {{-- <button x-on:click="loadNext('{{ $d }}')" class="mt-4 text-sm underline">Load next decade</button> --}}
      </section>
    @endforeach
  </main>
</div>
@endsection

@push('scripts')
<script>
function timelineTheme() {
  const decadeVars = {
    '1960s': { cls: 'theme-1960s' },
    '1970s': { cls: 'theme-1970s' },
    '1980s': { cls: 'theme-1980s' },
    '1990s': { cls: 'theme-1990s' },
    '2000s': { cls: 'theme-2000s' },
    '2010s': { cls: 'theme-2010s' },
    '2020s': { cls: 'theme-2020s' },
  };
  return {
    themeClass: 'theme-1960s',
    init(){ /* no-op */ },
    onDecadeEnter(d){ this.themeClass = decadeVars[d]?.cls ?? 'theme-1960s'; }
  }
}
</script>
@endpush