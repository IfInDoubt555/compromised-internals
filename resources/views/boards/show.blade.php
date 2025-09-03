@extends('layouts.app')

@section('content')
@php
  // Tailwind-safe color maps
  $paletteDot = [
    'slate'=>'bg-slate-500','red'=>'bg-red-500','amber'=>'bg-amber-500','green'=>'bg-green-500',
    'indigo'=>'bg-indigo-500','orange'=>'bg-orange-500','cyan'=>'bg-cyan-500','purple'=>'bg-purple-500',
    'emerald'=>'bg-emerald-500','blue'=>'bg-blue-500',
  ];
  $paletteRing = [
    'slate'=>'ring-slate-300/60','red'=>'ring-red-300/60','amber'=>'ring-amber-300/60','green'=>'ring-green-300/60',
    'indigo'=>'ring-indigo-300/60','orange'=>'ring-orange-300/60','cyan'=>'ring-cyan-300/60','purple'=>'ring-purple-300/60',
    'emerald'=>'ring-emerald-300/60','blue'=>'ring-blue-300/60',
  ];
  $tone = $board->color ?? 'slate';
  $dot  = $paletteDot[$tone] ?? 'bg-slate-500';
  $ring = $paletteRing[$tone] ?? 'ring-slate-300/60';

  $threadsCount = method_exists($board, 'threads') ? ($board->threads_count ?? $board->threads()->count()) : ($threads->total() ?? $threads->count());
@endphp

<section class="relative isolate">
  {{-- Light-only soft fade so content never sits right on the photo when short --}}
  <div aria-hidden="true"
       class="pointer-events-none absolute inset-x-0 -top-4 h-14
              rounded-t-2xl bg-gradient-to-b from-white/85 via-white/50 to-transparent
              dark:from-transparent"></div>

  <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-6 pb-24 min-h-[65vh]">

    {{-- HERO --}}
    <div class="rounded-2xl bg-white/85 ring-1 ring-black/5 shadow-xl backdrop-blur
                dark:bg-stone-900/70 dark:ring-white/10">
      <div class="p-6 sm:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <div class="flex items-center gap-2">
              <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
              <h1 class="font-orbitron text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight
                         text-stone-900 dark:text-stone-100">
                {{ $board->name }}
              </h1>
            </div>
            @if(!empty($board->description))
              <p class="mt-2 text-stone-700 dark:text-stone-300">
                {{ $board->description }}
              </p>
            @endif

            <div class="mt-3 text-xs text-stone-600 dark:text-stone-400">
              <span class="font-medium">{{ number_format($threadsCount) }}</span> threads
              @if(isset($board->created_at))
                • created {{ optional($board->created_at)->diffForHumans() }}
              @endif
            </div>
          </div>

          @auth
          <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('threads.create', $board->slug) }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold
                      bg-red-600 text-white shadow hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-400">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
              New Thread
            </a>

            <a href="{{ route('posts.create', ['board' => $board->slug]) }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold
                      bg-stone-900 text-white shadow hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-400
                      dark:bg-stone-700 dark:hover:bg-stone-600 dark:focus:ring-stone-500">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 17l6-6 4 4 6-6"/><path d="M19 13v6H5v-6"/></svg>
              New Blog Post
            </a>
          </div>
          @endauth
        </div>

        {{-- Tools: search + sort --}}
        <form method="GET" action="{{ route('boards.show', $board->slug) }}"
              class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto_auto]">
          <div class="relative">
            <input name="q" value="{{ request('q') }}"
                   placeholder="Search threads in {{ $board->name }}…"
                   class="w-full rounded-xl bg-white/90 pl-10 pr-3 py-2.5 text-sm ring-1 ring-black/10
                          placeholder-stone-500 shadow-sm
                          focus:outline-none focus:ring-2 focus:ring-sky-400
                          dark:bg-stone-800/70 dark:ring-white/10 dark:placeholder-stone-400" />
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-stone-500 dark:text-stone-400"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path>
            </svg>
          </div>

          <select name="sort"
                  class="rounded-xl bg-white/90 px-3 py-2.5 text-sm ring-1 ring-black/10 shadow-sm
                         focus:outline-none focus:ring-2 focus:ring-sky-400
                         dark:bg-stone-800/70 dark:ring-white/10">
            @php $s = request('sort','active'); @endphp
            <option value="active"  @selected($s==='active')>Sort: Last activity</option>
            <option value="latest"  @selected($s==='latest')>Sort: Newest</option>
            <option value="oldest"  @selected($s==='oldest')>Sort: Oldest</option>
            <option value="replies" @selected($s==='replies')>Sort: Most replies</option>
          </select>

          <button type="submit"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold
                         bg-stone-900 text-white shadow-sm hover:bg-stone-800
                         focus:outline-none focus:ring-2 focus:ring-stone-400
                         dark:bg-stone-700 dark:hover:bg-stone-600 dark:focus:ring-stone-500">
            Apply
          </button>
        </form>
      </div>

      <div class="h-px w-full bg-gradient-to-r from-transparent via-black/10 to-transparent dark:via-white/10"></div>

      {{-- Optional quick filters (visual only unless you wire them to query params) --}}
      <div class="px-6 py-3 sm:px-8 flex flex-wrap items-center gap-2 text-xs">
        @php
          $filter = request('filter','all');
          $chip = 'rounded-full px-3 py-1.5 ring-1 ring-black/10 bg-white/70 backdrop-blur hover:bg-white/90
                   dark:ring-white/10 dark:bg-stone-800/60 dark:hover:bg-stone-800 text-stone-700 dark:text-stone-300';
          $chipActive = 'bg-stone-900 text-white dark:bg-stone-700 dark:text-stone-100';
        @endphp
        <a href="{{ route('boards.show', $board->slug) }}" class="{{ $chip }} {{ $filter==='all' ? $chipActive : '' }}">All</a>
        <a href="{{ route('boards.show', [$board->slug,'filter'=>'hot']) }}" class="{{ $chip }} {{ $filter==='hot' ? $chipActive : '' }}">Hot</a>
        <a href="{{ route('boards.show', [$board->slug,'filter'=>'unanswered']) }}" class="{{ $chip }} {{ $filter==='unanswered' ? $chipActive : '' }}">Unanswered</a>
        <a href="{{ route('boards.show', [$board->slug,'filter'=>'mine']) }}" class="{{ $chip }} {{ $filter==='mine' ? $chipActive : '' }}">My threads</a>
      </div>
    </div>

    {{-- MAIN CONTENT: threads + (optional) featured posts --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-8 items-start">
      {{-- Threads list --}}
      <div class="rounded-2xl bg-white/85 ring-1 ring-black/5 shadow-md backdrop-blur
                  dark:bg-stone-900/70 dark:ring-white/10">
        @forelse($threads as $t)
          @php
            $author   = optional($t->user);
            $name     = $author->display_name ?? $author->name ?? 'Unknown';
            $avatar   = $author->profile_photo_url ?? null; // ok if null
            $replies  = $t->replies_count ?? (method_exists($t,'replies') ? $t->replies()->count() : 0);
            $activity = $t->last_activity_at ?? $t->updated_at ?? $t->created_at;
          @endphp

          <a href="{{ route('threads.show', $t->slug) }}"
             class="block px-5 sm:px-6 py-4 border-b last:border-b-0 border-black/5
                    hover:bg-white/70 dark:hover:bg-stone-800/40 dark:border-white/10">
            <div class="flex items-start gap-3">
              {{-- Avatar (fallback circle) --}}
              <div class="h-9 w-9 rounded-full ring-1 ring-black/10 overflow-hidden shrink-0
                          bg-stone-200 dark:bg-stone-700 dark:ring-white/10">
                @if($avatar)
                  <img src="{{ $avatar }}" alt="" class="h-full w-full object-cover" loading="lazy">
                @else
                  <div class="h-full w-full grid place-items-center text-xs text-stone-600 dark:text-stone-300">
                    {{ strtoupper(substr($name,0,1)) }}
                  </div>
                @endif
              </div>

              <div class="min-w-0 flex-1">
                <p class="font-semibold text-stone-900 dark:text-stone-100 line-clamp-2">
                  {{ $t->title }}
                </p>

                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-stone-600 dark:text-stone-400">
                  <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M5 8h14M5 12h14M5 16h10"/>
                    </svg>
                    {{ $name }}
                  </span>

                  <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    {{ number_format($replies) }} replies
                  </span>

                  @if($activity)
                  <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/>
                    </svg>
                    updated {{ optional($activity)->diffForHumans() }}
                  </span>
                  @endif
                </div>
              </div>

              {{-- count chip --}}
              <span class="ml-2 inline-flex items-center rounded-full px-2 py-1 text-[11px] font-medium
                           bg-stone-900 text-white dark:bg-stone-700">
                {{ number_format($replies) }}
              </span>
            </div>
          </a>
        @empty
          <div class="px-6 py-8 text-center">
            <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">No threads yet</h3>
            <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">
              Be the first to start a conversation.
              @auth
              <a href="{{ route('threads.create', $board->slug) }}" class="font-semibold text-red-600 underline dark:text-rose-300">Create a thread</a>.
              @endauth
            </p>
          </div>
        @endforelse
      </div>

      {{-- Sidebar: Featured Blog Posts (if any) --}}
      @if($posts->isNotEmpty())
        <aside class="space-y-3">
          <div class="rounded-2xl bg-white/85 ring-1 ring-black/5 shadow-md backdrop-blur
                      dark:bg-stone-900/70 dark:ring-white/10">
            <div class="p-5 sm:p-6">
              <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-700 dark:text-stone-300">
                Featured posts
              </h2>

              <div class="mt-4 space-y-3">
                @foreach($posts as $p)
                  <a href="{{ route('blog.show', $p->slug) }}"
                     class="group block rounded-xl ring-1 ring-black/10 bg-white/70 p-3
                            hover:bg-white/90 transition
                            dark:bg-stone-800/60 dark:ring-white/10 dark:hover:bg-stone-800">
                    <p class="font-semibold text-sm text-stone-900 group-hover:text-stone-700
                              dark:text-stone-100 dark:group-hover:text-stone-100 line-clamp-2">
                      {{ $p->title }}
                    </p>
                    <p class="mt-1 text-[11px] text-stone-500 dark:text-stone-400">
                      {{ optional($p->created_at)->format('M j, Y') }}
                    </p>
                  </a>
                @endforeach
              </div>
            </div>
          </div>

          @auth
          {{-- Quick action card --}}
          <div class="rounded-2xl bg-white/85 ring-1 ring-black/5 shadow-md p-4 backdrop-blur
                      dark:bg-stone-900/70 dark:ring-white/10">
            <div class="flex gap-2">
              <a href="{{ route('threads.create', $board->slug) }}"
                 class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold
                        bg-red-600 text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                New Thread
              </a>
              <a href="{{ route('posts.create', ['board' => $board->slug]) }}"
                 class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold
                        bg-stone-900 text-white hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-400
                        dark:bg-stone-700 dark:hover:bg-stone-600 dark:focus:ring-stone-500">
                <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M4 17l6-6 4 4 6-6"/><path d="M19 13v6H5v-6"/></svg>
                New Post
              </a>
            </div>
          </div>
          @endauth
        </aside>
      @endif
    </div>

    {{-- Pagination --}}
    <div class="mt-8 flex justify-center">
      <div class="rounded-xl bg-white/85 ring-1 ring-black/5 px-3 py-2 shadow backdrop-blur
                  dark:bg-stone-900/70 dark:ring-white/10">
        {{ $threads->links() }}
      </div>
    </div>
  </div>
</section>
@endsection