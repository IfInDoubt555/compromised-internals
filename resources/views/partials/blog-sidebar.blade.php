<!-- resources/views/partials/blog-sidebar.blade.php -->
<aside class="sticky top-24 space-y-6">
    {{-- Search within posts (or threads later) --}}
    <form action="{{ route('blog.index') }}" method="GET" class="flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Search posts or tags"
               class="w-full rounded-lg border border-gray-300 bg-white/70 px-3 py-2 outline-none focus:ring" />
        <button class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Search</button>
    </form>

    {{-- Discussion Boards --}}
    <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow">
        <h3 class="mb-3 text-lg font-bold">Discussion Boards</h3>
        <ul class="divide-y divide-gray-200">
            @foreach(\App\Models\Board::orderBy('position')->withCount('threads')->get() as $b)
                <li>
                    <a href="{{ route('boards.show', $b->slug) }}"
                       class="flex items-center justify-between py-3 hover:opacity-90">
                        <span class="flex items-center gap-2">
                            {{-- simple icon dot using Tailwind color --}}
                            <span class="h-2 w-2 rounded-full bg-{{ $b->color ?? 'slate' }}-500"></span>
                            <span>{{ $b->name }}</span>
                        </span>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold">{{ $b->threads_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('boards.index') }}" class="mt-3 inline-block text-sm font-semibold text-gray-600 hover:text-gray-800">View all</a>
    </div>

    {{-- Recent threads (optional) --}}
    <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow">
        <h3 class="mb-3 text-lg font-bold">Hot Right Now</h3>
        <ul class="space-y-3">
            @foreach(\App\Models\Thread::latest('last_activity_at')->take(5)->get() as $t)
                <li>
                    <a href="{{ route('threads.show', $t->slug) }}" class="block">
                        <p class="line-clamp-2 font-medium">{{ $t->title }}</p>
                        <p class="text-xs text-gray-500">{{ $t->board->name }} • {{ $t->last_activity_at?->diffForHumans() }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- CTA --}}
    @auth
    <a href="{{ route('boards.index') }}"
       class="flex w-full items-center justify-center rounded-xl bg-red-600 px-4 py-3 font-semibold text-white shadow hover:bg-red-700">
       Start a Discussion
    </a>
    @endauth
</aside>
