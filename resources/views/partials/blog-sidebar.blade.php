<div class="space-y-6">

  {{-- Search --}}
  <form action="{{ route('blog.index') }}" method="GET" class="ci-card p-3">
    <label class="sr-only" for="blog-search">Search posts or tags</label>
    <div class="flex items-center gap-2">
      <div class="relative w-full">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 ci-muted"
             viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M15.5 14h-.79l-.28-.27a6.471 6.471 0 1 0-.71.71l.27.28v.79L20 21.49 21.49 20l-5.99-6ZM10 15a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
        </svg>
        <input
          id="blog-search"
          name="tag"
          value="{{ request('tag') }}"
          placeholder="Search posts or tags"
          class="w-full rounded-xl border border-stone-300 dark:border-white/10
                 bg-white/90 dark:bg-stone-800/60
                 px-9 py-2 text-sm
                 text-stone-900 dark:text-stone-100
                 placeholder-stone-400 dark:placeholder-stone-500
                 outline-none focus:ring-2 focus:ring-sky-400/50 focus:border-sky-400"
        />
      </div>
      <button
        class="ci-btn-sky ring-1 ring-stone-900/5 dark:ring-white/10">
        Search
      </button>
    </div>
  </form>

  {{-- Discussion Boards --}}
  <section class="ci-card p-4">
    @php
      /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Board> $boards */
      $boards = \App\Models\Board::query()
          ->withCount('threads')
          ->orderBy('position')
          ->get();      
      $palette = [
          'slate'=>'bg-slate-500','red'=>'bg-red-500','amber'=>'bg-amber-500','green'=>'bg-green-500',
          'indigo'=>'bg-indigo-500','orange'=>'bg-orange-500','cyan'=>'bg-cyan-500','purple'=>'bg-purple-500',
          'emerald'=>'bg-emerald-500','blue'=>'bg-blue-500',
      ];
    @endphp

    {{-- Mobile: collapsible --}}
    <details class="lg:hidden">
      <summary class="flex cursor-pointer select-none items-center justify-between text-base font-semibold ci-body">
        Discussion Boards
        <svg class="h-5 w-5 ci-muted transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
        </svg>
      </summary>

      <ul class="mt-3 divide-y ci-divider">
        @forelse($boards as $b)
          @php $dot = $palette[$b->color ?? 'slate'] ?? 'bg-slate-500'; @endphp
          <li>
            <a href="{{ route('boards.show', $b->slug) }}"
               class="flex items-center justify-between py-3 hover:opacity-95">
              <span class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                <span class="text-sm ci-body">{{ $b->name }}</span>
              </span>
              @if(($b->threads_count ?? 0) > 0)
                <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold
                             bg-stone-100 dark:bg-stone-800/60
                             text-stone-700 dark:text-stone-200
                             ring-1 ring-stone-900/5 dark:ring-white/10">
                  {{ $b->threads_count }}
                </span>
              @endif
            </a>
          </li>
        @empty
          <li class="py-3 text-sm ci-muted">No boards yet.</li>
        @endforelse
      </ul>

      <a href="{{ route('boards.index') }}"
         class="mt-3 inline-block text-sm font-semibold ci-cta">View all</a>
    </details>

    {{-- Desktop --}}
    <div class="hidden lg:block">
      <h3 class="mb-3 text-base font-semibold ci-body">Discussion Boards</h3>
      <ul class="divide-y ci-divider">
        @foreach($boards as $b)
          @php $dot = $palette[$b->color ?? 'slate'] ?? 'bg-slate-500'; @endphp
          <li>
            <a href="{{ route('boards.show', $b->slug) }}"
               class="flex items-center justify-between py-3 hover:opacity-95">
              <span class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                <span class="text-sm ci-body">{{ $b->name }}</span>
              </span>
              @if(($b->threads_count ?? 0) > 0)
                <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold
                             bg-stone-100 dark:bg-stone-800/60
                             text-stone-700 dark:text-stone-200
                             ring-1 ring-stone-900/5 dark:ring-white/10">
                  {{ $b->threads_count }}
                </span>
              @endif
            </a>
          </li>
        @endforeach
      </ul>
      <a href="{{ route('boards.index') }}"
         class="mt-3 inline-block text-sm font-semibold ci-cta">View all</a>
    </div>
  </section>

  {{-- Recent threads --}}
  <section class="ci-card p-4">
    <h3 class="mb-3 text-base font-semibold ci-body">Hot Right Now</h3>
    <ul class="space-y-3">
      @foreach(\App\Models\Thread::latest('last_activity_at')->take(5)->get() as $t)
        <li>
          <a href="{{ route('threads.show', $t->slug) }}"
             class="block rounded-xl p-3 transition
                    bg-white/70 dark:bg-stone-800/60
                    ring-1 ring-stone-900/5 dark:ring-white/10 hover:brightness-110">
            <p class="line-clamp-2 text-sm font-medium ci-body">{{ $t->title }}</p>
            <p class="mt-1 text-[11px] ci-muted">
              {{ $t->board->name }} • {{ $t->last_activity_at?->diffForHumans() }}
            </p>
          </a>
        </li>
      @endforeach
      @if(!\App\Models\Thread::exists())
        <li class="text-sm ci-muted">No active threads yet.</li>
      @endif
    </ul>
  </section>
</div>