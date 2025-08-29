@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    @php
        $palette = [
            'slate'=>'bg-slate-500','red'=>'bg-red-500','amber'=>'bg-amber-500','green'=>'bg-green-500',
            'indigo'=>'bg-indigo-500','orange'=>'bg-orange-500','cyan'=>'bg-cyan-500','purple'=>'bg-purple-500',
            'emerald'=>'bg-emerald-500','blue'=>'bg-blue-500',
        ];
        $dot = $palette[$board->color ?? 'slate'] ?? 'bg-slate-500';
    @endphp

    <div class="mb-6 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
            <h1 class="text-2xl font-bold text-stone-900 dark:text-stone-100">{{ $board->name }}</h1>
        </div>

        @auth
        <div class="flex items-center gap-2">
            {{-- Thread (board-native) --}}
            <a href="{{ route('threads.create', $board->slug) }}"
               class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                New Thread
            </a>
            {{-- Blog post, tagged to this board --}}
            <a href="{{ route('posts.create', ['board' => $board->slug]) }}"
               class="rounded-lg bg-gray-700 px-4 py-2 text-white hover:bg-gray-800
                      dark:bg-stone-800 dark:hover:bg-stone-700 dark:ring-1 dark:ring-white/10">
                New Blog Post
            </a>
        </div>
        @endauth
    </div>

    {{-- Threads list --}}
    <div class="divide-y rounded-xl border border-gray-200 bg-white/80 shadow
                dark:bg-stone-900/70 dark:border-white/10 dark:divide-white/10">
        @forelse($threads as $t)
            <a href="{{ route('threads.show', $t->slug) }}"
               class="block px-5 py-4 hover:bg-gray-50 dark:hover:bg-stone-800/40">
                <p class="font-semibold text-stone-900 dark:text-stone-100">{{ $t->title }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-stone-500">
                    by {{ $t->user->display_name ?? $t->user->name ?? 'Unknown' }}
                    • {{ $t->replies_count ?? $t->replies()->count() }} replies
                    • {{ optional($t->last_activity_at)->diffForHumans() }}
                </p>
            </a>
        @empty
            <p class="px-5 py-6 text-sm text-gray-600 dark:text-stone-400">
                No threads yet. Be the first!
                @auth
                    <a href="{{ route('threads.create', $board->slug) }}"
                       class="text-red-600 underline dark:text-rose-300">Start one</a>.
                @endauth
            </p>
        @endforelse
    </div>

    {{-- Featured Blog Posts linked to this board --}}
    @if($posts->isNotEmpty())
        <div class="mt-6 rounded-xl border border-gray-200 bg-white/80 shadow p-4
                    dark:bg-stone-900/70 dark:border-white/10">
            <h2 class="mb-3 text-lg font-bold text-stone-900 dark:text-stone-100">Featured Blog Posts</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($posts as $p)
                    <a href="{{ route('blog.show', $p->slug) }}"
                       class="block rounded-lg border bg-white p-3 hover:bg-gray-50
                              dark:bg-stone-800/60 dark:border-white/10 dark:hover:bg-stone-800">
                        <p class="font-semibold line-clamp-2 text-stone-900 dark:text-stone-100">{{ $p->title }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-stone-500">{{ $p->created_at->format('M j, Y') }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Threads pagination --}}
    <div class="mt-6">
        {{ $threads->links() }}
    </div>
</div>
@endsection