{{-- resources/views/history/timeline.blade.php --}}
@extends('layouts.app')

@section('content')
    <div id="theme-wrapper"
         x-data="timelineTheme()"
         x-init="init()"
         :class="themeClass"
         class="min-h-screen">  <header class="max-w-5xl mx-auto px-4 py-6">
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
            <li class="p-3 history-card hover:bg-white/70 transition">
            <a href="{{ route('history.show', ['tab' => 'events', 'decade' => $d, 'id' => $e['id']]) }}"
               class="font-medium hover:underline">
              {{ $e['title'] }}
            </a>
            <p class="text-sm opacity-80">{{ $e['bio'] ?? '' }}</p>
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
  const map = {
    '1960s': 'decade-1960',
    '1970s': 'decade-1970',
    '1980s': 'decade-1980',
    '1990s': 'decade-1990',
    '2000s': 'decade-2000',
    '2010s': 'decade-2010',
    '2020s': 'decade-2020',
  };
  return {
    themeClass: 'decade-1960',
    init(){},
    onDecadeEnter(d){ this.themeClass = map[d] ?? 'decade-1960'; }
  }
}
</script>
@endpush