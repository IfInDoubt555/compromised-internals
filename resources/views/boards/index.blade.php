@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Discussion Boards</h1>
        <p class="text-gray-600">Pick a board to dive in.</p>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($boards as $b)
            <a href="{{ route('boards.show', $b->slug) }}"
               class="rounded-xl border border-gray-200 bg-white/80 p-5 shadow hover:shadow-md transition">
                <div class="mb-2 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-{{ $b->color ?? 'slate' }}-500"></span>
                    <h2 class="text-xl font-semibold">{{ $b->name }}</h2>
                </div>
                @if($b->description)
                    <p class="text-sm text-gray-600">{{ $b->description }}</p>
                @endif
                <p class="mt-3 text-xs text-gray-500">{{ $b->threads_count ?? $b->threads()->count() }} threads</p>
            </a>
        @empty
            <p class="text-gray-600">No boards yet.</p>
        @endforelse
    </div>
</div>
@endsection
