@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-{{ $board->color ?? 'slate' }}-500"></span>
            <h1 class="text-2xl font-bold">{{ $board->name }}</h1>
        </div>
        @auth
            <a href="{{ route('threads.create', $board->slug) }}"class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                New Thread
            </a>
        @endauth
    </div>

    <div class="rounded-xl border border-gray-200 bg-white/80 shadow divide-y">
        @forelse($threads as $t)
            <a href="{{ route('threads.show', $t->slug) }}" class="block px-5 py-4 hover:bg-gray-50">
                <p class="font-semibold">{{ $t->title }}</p>
                <p class="mt-1 text-xs text-gray-500">
                    by {{ $t->user->display_name ?? $t->user->name ?? 'Unknown' }}
                    • {{ $t->replies_count ?? $t->replies()->count() }} replies
                    • {{ optional($t->last_activity_at)->diffForHumans() }}
                </p>
            </a>
        @empty
            <p class="px-5 py-6 text-sm text-gray-600">No threads yet. Be the first!</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $threads->links() }}
    </div>
</div>
@endsection
