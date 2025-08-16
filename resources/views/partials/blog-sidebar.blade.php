<aside class="space-y-6">
    {{-- Search (this replaces the top-of-page search) --}}
    <form action="{{ route('blog.index') }}" method="GET" class="flex gap-2">
        <input
            name="tag"
            value="{{ request('tag') }}"
            placeholder="Search posts or tags"
            aria-label="Search posts or tags"
            class="w-full rounded-lg border border-gray-300 bg-white/70 px-3 py-2 outline-none focus:ring"
        />
        <button class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Search</button>
    </form>

    {{-- Discussion Boards (boxed + always a vertical list) --}}
    <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow">
        <h3 class="mb-3 text-lg font-bold">Discussion Boards</h3>

        @php
            $boards = \App\Models\Board::orderBy('position')->withCount('threads')->get();
            $palette = [
                'slate'=>'bg-slate-500','red'=>'bg-red-500','amber'=>'bg-amber-500','green'=>'bg-green-500',
                'indigo'=>'bg-indigo-500','orange'=>'bg-orange-500','cyan'=>'bg-cyan-500','purple'=>'bg-purple-500',
                'emerald'=>'bg-emerald-500','blue'=>'bg-blue-500',
            ];
        @endphp

        <ul class="divide-y divide-gray-200">
            @forelse($boards as $b)
                @php $dot = $palette[$b->color ?? 'slate'] ?? 'bg-slate-500'; @endphp
                <li>
                    <a href="{{ route('boards.show', $b->slug) }}"
                       class="flex items-center justify-between py-3 hover:opacity-90">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                            <span>{{ $b->name }}</span>
                        </span>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold">
                            {{ $b->threads_count }}
                        </span>
                    </a>
                </li>
            @empty
                <li class="py-3 text-sm text-gray-500">
                    No boards yet.
                </li>
            @endforelse
        </ul>

        <a href="{{ route('boards.index') }}"
           class="mt-3 inline-block text-sm font-semibold text-gray-600 hover:text-gray-800">View all</a>
    </div>

    {{-- Recent threads --}}
    <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow">
        <h3 class="mb-3 text-lg font-bold">Hot Right Now</h3>
        <ul class="space-y-3">
            @foreach(\App\Models\Thread::latest('last_activity_at')->take(5)->get() as $t)
                <li>
                    <a href="{{ route('threads.show', $t->slug) }}" class="block">
                        <p class="line-clamp-2 font-medium">{{ $t->title }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $t->board->name }} • {{ $t->last_activity_at?->diffForHumans() }}
                        </p>
                    </a>
                </li>
            @endforeach
            @if(!\App\Models\Thread::exists())
                <li class="text-sm text-gray-500">No active threads yet.</li>
            @endif
        </ul>
    </div>
</aside>