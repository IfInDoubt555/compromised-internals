@extends('layouts.app')

@section('content')
@php
  // Tailwind-safe color maps (dot + subtle ring tint on hover/focus)
  $paletteDot = [
    'slate'   => 'bg-slate-500',  'red'     => 'bg-red-500',   'amber'  => 'bg-amber-500',
    'green'   => 'bg-green-500',  'indigo'  => 'bg-indigo-500','orange' => 'bg-orange-500',
    'cyan'    => 'bg-cyan-500',   'purple'  => 'bg-purple-500','emerald'=> 'bg-emerald-500',
    'blue'    => 'bg-blue-500',
  ];
  $paletteRing = [
    'slate'   => 'hover:ring-slate-300/60 focus-visible:ring-slate-300/60',
    'red'     => 'hover:ring-red-300/60 focus-visible:ring-red-300/60',
    'amber'   => 'hover:ring-amber-300/60 focus-visible:ring-amber-300/60',
    'green'   => 'hover:ring-green-300/60 focus-visible:ring-green-300/60',
    'indigo'  => 'hover:ring-indigo-300/60 focus-visible:ring-indigo-300/60',
    'orange'  => 'hover:ring-orange-300/60 focus-visible:ring-orange-300/60',
    'cyan'    => 'hover:ring-cyan-300/60 focus-visible:ring-cyan-300/60',
    'purple'  => 'hover:ring-purple-300/60 focus-visible:ring-purple-300/60',
    'emerald' => 'hover:ring-emerald-300/60 focus-visible:ring-emerald-300/60',
    'blue'    => 'hover:ring-blue-300/60 focus-visible:ring-blue-300/60',
  ];
@endphp

<section class="relative isolate">
  {{-- Light-only soft fade so text never sits right on the photo when content is short --}}
  <div aria-hidden="true"
       class="pointer-events-none absolute inset-x-0 -top-4 h-14
              rounded-t-2xl bg-gradient-to-b from-white/85 via-white/50 to-transparent
              dark:from-transparent"></div>

  {{-- PAGE CONTAINER --}}
  <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-6 pb-24 min-h-[65vh]">

    {{-- HERO / INTRO --}}
    <div
      class="rounded-2xl bg-white/85 ring-1 ring-black/5 shadow-xl backdrop-blur mb-8
             dark:bg-stone-900/70 dark:ring-white/10">
      <div class="p-6 sm:p-8">
        <h1 class="font-orbitron text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight
                   text-stone-900 dark:text-stone-100">
          Discussion Boards
        </h1>
        <p class="mt-2 text-stone-700 dark:text-stone-300">
          Pick a board to dive in. Share tips, swap stories, and geek out with other rally fans.
        </p>

        {{-- Search row (non-blocking; submits ?q=) --}}
        <form method="GET" action="{{ route('boards.index') }}"
              class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
          <div class="relative flex-1">
            <input
              name="q"
              value="{{ request('q') }}"
              placeholder="Search boards…"
              class="w-full rounded-xl bg-white/90 pl-10 pr-3 py-2.5 text-sm ring-1 ring-black/10
                     placeholder-stone-500 shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-sky-400
                     dark:bg-stone-800/70 dark:ring-white/10 dark:placeholder-stone-400" />
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-stone-500 dark:text-stone-400"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M20 20l-3.5-3.5"></path>
            </svg>
          </div>
          <button type="submit"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold
                         bg-stone-900 text-white shadow-sm hover:bg-stone-800
                         focus:outline-none focus:ring-2 focus:ring-stone-400
                         dark:bg-stone-700 dark:hover:bg-stone-600 dark:focus:ring-stone-500">
            Search
          </button>
        </form>
      </div>

      <div class="h-px w-full bg-gradient-to-r from-transparent via-black/10 to-transparent
                  dark:via-white/10"></div>

      {{-- Tiny stats line (optional) --}}
      <div class="px-6 py-3 sm:px-8 text-xs text-stone-600 dark:text-stone-400">
        <span class="font-medium">{{ $boards->count() }}</span> boards
        @if(method_exists($boards, 'total') && $boards->total() !== null)
          • showing {{ $boards->count() }} of {{ $boards->total() }}
        @endif
      </div>
    </div>

    {{-- GRID --}}
    <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($boards as $b)
        @php
          $tone = $b->color ?? 'slate';
          $dot  = $paletteDot[$tone] ?? 'bg-slate-500';
          $ring = $paletteRing[$tone] ?? 'hover:ring-slate-300/60 focus-visible:ring-slate-300/60';
          $threadsCount = $b->threads_count ?? (method_exists($b, 'threads') ? $b->threads()->count() : 0);
        @endphp

        <li>
          <a href="{{ route('boards.show', $b->slug) }}"
             class="group block h-full rounded-2xl bg-white/85 ring-1 ring-black/5 shadow-md backdrop-blur
                    transition transform hover:-translate-y-0.5 hover:shadow-xl focus:outline-none
                    focus-visible:ring-2 {{ $ring }}
                    dark:bg-stone-900/70 dark:ring-white/10">
            <div class="p-5 sm:p-6 flex h-full flex-col">
              <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                <h2 class="text-lg sm:text-xl font-semibold text-stone-900 dark:text-stone-100">
                  {{ $b->name }}
                </h2>

                {{-- chevron floats right on hover --}}
                <svg class="ml-auto h-5 w-5 shrink-0 text-stone-400 transition group-hover:translate-x-0.5
                            group-hover:text-stone-600 dark:group-hover:text-stone-300"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
              </div>

              @if($b->description)
                <p class="mt-2 text-sm leading-relaxed text-stone-700 dark:text-stone-300">
                  {{ $b->description }}
                </p>
              @endif

              <div class="mt-4 flex items-center justify-between text-xs text-stone-600 dark:text-stone-400">
                <span class="inline-flex items-center gap-1">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                  {{ number_format($threadsCount) }} threads
                </span>

                {{-- “Enter board” micro-cta --}}
                <span class="inline-flex items-center gap-1 font-medium text-stone-800 group-hover:text-stone-900
                             dark:text-stone-200 dark:group-hover:text-stone-100">
                  Enter
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                  </svg>
                </span>
              </div>
            </div>
          </a>
        </li>
      @empty
        <li class="col-span-full">
          <div class="rounded-2xl bg-white/85 ring-1 ring-black/5 p-8 text-center shadow
                      dark:bg-stone-900/70 dark:ring-white/10">
            <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">No boards yet</h3>
            <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">
              Check back soon—new boards are on the way.
            </p>
          </div>
        </li>
      @endforelse
    </ul>
  </div>
</section>
@endsection