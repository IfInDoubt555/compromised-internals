<aside class="space-y-6">

  {{-- Search --}}
  <form action="{{ route('blog.index') }}" method="GET"
        class="rounded-2xl border border-slate-200 bg-white/80 backdrop-blur-sm shadow-sm p-3">
    <label class="sr-only" for="blog-search">Search posts or tags</label>
    <div class="flex items-center gap-2">
      <div class="relative w-full">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
             viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M15.5 14h-.79l-.28-.27a6.471 6.471 0 1 0-.71.71l.27.28v.79L20 21.49 21.49 20l-5.99-6ZM10 15a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
        </svg>
        <input
          id="blog-search"
          name="tag"
          value="{{ request('tag') }}"
          placeholder="Search posts or tags"
          class="w-full rounded-xl border border-slate-200 bg-white/90 px-9 py-2 text-sm outline-none ring-0 focus:border-slate-300"
        />
      </div>
      <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
        Search
      </button>
    </div>
  </form>

  {{-- Discussion Boards --}}
  <section class="rounded-2xl border border-slate-200 bg-white/80 backdrop-blur-sm p-4 shadow-sm">
    @php
      $boards = \App\Models\Board::orderBy('position')->withCount('threads')->get();
      $palette = [
          'slate'=>'bg-slate-500','red'=>'bg-red-500','amber'=>'bg-amber-500','green'=>'bg-green-500',
          'indigo'=>'bg-indigo-500','orange'=>'bg-orange-500','cyan'=>'bg-cyan-500','purple'=>'bg-purple-500',
          'emerald'=>'bg-emerald-500','blue'=>'bg-blue-500',
      ];
    @endphp

    {{-- Mobile: collapsible --}}
    <details class="lg:hidden">
      <summary class="flex cursor-pointer select-none items-center justify-between text-base font-semibold text-slate-900">
        Discussion Boards
        <svg class="h-5 w-5 text-slate-500 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
        </svg>
      </summary>

      <ul class="mt-3 divide-y divide-slate-200">
        @forelse($boards as $b)
          @php $dot = $palette[$b->color ?? 'slate'] ?? 'bg-slate-500'; @endphp
          <li>
            <a href="{{ route('boards.show', $b->slug) }}"
               class="flex items-center justify-between py-3 hover:opacity-95">
              <span class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                <span class="text-sm text-slate-800">{{ $b->name }}</span>
              </span>
              @if(($b->threads_count ?? 0) > 0)
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                  {{ $b->threads_count }}
                </span>
              @endif
            </a>
          </li>
        @empty
          <li class="py-3 text-sm text-slate-500">No boards yet.</li>
        @endforelse
      </ul>

      <a href="{{ route('boards.index') }}"
         class="mt-3 inline-block text-sm font-semibold text-slate-600 hover:text-slate-800">View all</a>
    </details>

    {{-- Desktop --}}
    <div class="hidden lg:block">
      <h3 class="mb-3 text-base font-semibold text-slate-900">Discussion Boards</h3>
      <ul class="divide-y divide-slate-200">
        @foreach($boards as $b)
          @php $dot = $palette[$b->color ?? 'slate'] ?? 'bg-slate-500'; @endphp
          <li>
            <a href="{{ route('boards.show', $b->slug) }}"
               class="flex items-center justify-between py-3 hover:opacity-95">
              <span class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                <span class="text-sm text-slate-800">{{ $b->name }}</span>
              </span>
              @if(($b->threads_count ?? 0) > 0)
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                  {{ $b->threads_count }}
                </span>
              @endif
            </a>
          </li>
        @endforeach
      </ul>
      <a href="{{ route('boards.index') }}"
         class="mt-3 inline-block text-sm font-semibold text-slate-600 hover:text-slate-800">View all</a>
    </div>
  </section>

  {{-- Recent threads --}}
  <section class="rounded-2xl border border-slate-200 bg-white/80 backdrop-blur-sm p-4 shadow-sm">
    <h3 class="mb-3 text-base font-semibold text-slate-900">Hot Right Now</h3>
    <ul class="space-y-3">
      @foreach(\App\Models\Thread::latest('last_activity_at')->take(5)->get() as $t)
        <li>
          <a href="{{ route('threads.show', $t->slug) }}" class="block rounded-xl border border-slate-200/70 bg-white/70 p-3 hover:bg-white">
            <p class="line-clamp-2 text-sm font-medium text-slate-900">{{ $t->title }}</p>
            <p class="mt-1 text-[11px] text-slate-500">
              {{ $t->board->name }} • {{ $t->last_activity_at?->diffForHumans() }}
            </p>
          </a>
        </li>
      @endforeach
      @if(!\App\Models\Thread::exists())
        <li class="text-sm text-slate-500">No active threads yet.</li>
      @endif
    </ul>
  </section>
</aside>