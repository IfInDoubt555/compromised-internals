{{-- resources/views/history/bookmarks.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="{ decade: '{{ $decade }}', openYear: '{{ $year ?? '' }}' }"
     :class="`theme-${decade}`" class="min-h-screen">
  <header class="max-w-6xl mx-auto px-4 py-6 flex items-center gap-3">
    <h1 class="text-3xl font-bold">Rally History Archive</h1>
    <a href="{{ route('history.index', ['view' => 'timeline']) }}" class="text-sm underline">Try Timeline →</a>
  </header>

  <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-[140px_1fr] gap-6">
    {{-- Bookmarks (decade tabs) --}}
    <nav class="relative">
      <ul class="flex md:flex-col gap-2">
        @foreach($decades as $d)
          @php $active = $d === $decade; @endphp
          <li>
            <a href="{{ route('history.index', ['view'=>'bookmarks','decade'=>$d]) }}"
               class="group relative block px-4 py-2 font-semibold
                      bg-[var(--bg)] border-l-4
                      {{ $active ? 'border-[var(--accent)] translate-x-0' : 'border-transparent hover:translate-x-1' }}
                      rounded-r-lg shadow-sm transition">
              <span class="inline-block -skew-x-6">{{ $d }}</span>
              <span class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3
                           bg-[var(--accent)] rounded-tr-lg"></span> {{-- bookmark nib --}}
            </a>
          </li>
        @endforeach
      </ul>
    </nav>

    {{-- Main panel --}}
    <section>
      {{-- Year chip row --}}
      <div class="flex flex-wrap gap-2 mb-4">
        @foreach($years as $y)
          <a href="{{ route('history.index', ['view'=>'bookmarks','decade'=>$decade,'year'=>$y]) }}"
             class="px-3 py-1 rounded-full border text-sm
                    {{ (string)$y === (string)($year ?? '') ? 'bg-[var(--accent)] text-white' : 'hover:bg-neutral-100' }}">
            {{ $y }}
          </a>
        @endforeach
      </div>

      {{-- Results list (no cards) --}}
      <ul class="divide-y border rounded-md overflow-hidden">
        @foreach($items as $e)
          <li class="p-3">
            <a href="{{ route('history.show', $e->slug) }}" class="font-medium hover:underline">
              {{ $e->year }} — {{ $e->title }}
            </a>
            <p class="text-sm text-neutral-600 line-clamp-2">{{ $e->bio }}</p>
          </li>
        @endforeach
      </ul>
    </section>
  </div>
</div>
@endsection