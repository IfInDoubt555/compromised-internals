@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-stone-900 dark:text-stone-100">Discussion Boards</h1>
        <p class="text-gray-600 dark:text-stone-400">Pick a board to dive in.</p>
    </div>

    @php
      // Safe mapping so Purge doesn't drop classes
      $palette = [
        'slate'   => 'bg-slate-500',
        'red'     => 'bg-red-500',
        'amber'   => 'bg-amber-500',
        'green'   => 'bg-green-500',
        'indigo'  => 'bg-indigo-500',
        'orange'  => 'bg-orange-500',
        'cyan'    => 'bg-cyan-500',
        'purple'  => 'bg-purple-500',
        'emerald' => 'bg-emerald-500',
        'blue'    => 'bg-blue-500',
      ];
    @endphp

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($boards as $b)
            @php $dot = $palette[$b->color ?? 'slate'] ?? 'bg-slate-500'; @endphp
            <a href="{{ route('boards.show', $b->slug) }}"
               class="group rounded-xl border border-gray-200 bg-white/80 p-5 shadow transition
                      hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400
                      dark:bg-stone-900/70 dark:border-white/10 dark:shadow-none dark:hover:bg-stone-900/80
                      dark:focus-visible:ring-sky-300">
                <div class="mb-2 flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">
                        {{ $b->name }}
                    </h2>
                </div>

                @if($b->description)
                    <p class="text-sm text-gray-600 dark:text-stone-400">
                        {{ $b->description }}
                    </p>
                @endif

                <p class="mt-3 text-xs text-gray-500 dark:text-stone-500">
                    {{ $b->threads_count ?? $b->threads()->count() }} threads
                </p>
            </a>
        @empty
            <p class="text-gray-600 dark:text-stone-400">No boards yet.</p>
        @endforelse
    </div>
</div>
@endsection