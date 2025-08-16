@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- Thread header --}}
    <div class="mb-6">
        <a href="{{ route('boards.show', $thread->board->slug) }}" class="text-sm text-red-600 hover:underline">
            ← {{ $thread->board->name }}
        </a>
        <h1 class="mt-2 text-3xl font-bold">{{ $thread->title }}</h1>
        <p class="mt-1 text-xs text-gray-500">
            by {{ $thread->user->display_name ?? $thread->user->name ?? 'Unknown' }}
            • {{ optional($thread->created_at)->format('M j, Y') }}
            • last activity {{ optional($thread->last_activity_at)->diffForHumans() }}
        </p>
    </div>

    {{-- Thread body --}}
    <article class="prose max-w-none rounded-xl border border-gray-200 bg-white/80 p-6 shadow">
        {!! nl2br(e($thread->body)) !!}
    </article>

    {{-- Replies --}}
    <section class="mt-8">
        <h2 class="mb-4 text-xl font-semibold">Replies ({{ $thread->replies->count() }})</h2>

        <div class="space-y-4">
            @forelse($thread->replies as $reply)
                <div class="rounded-xl border border-gray-200 bg-white/70 p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $reply->body }}</p>
                    <p class="mt-2 text-xs text-gray-500">
                        — {{ $reply->user->display_name ?? $reply->user->name ?? 'Unknown' }},
                        {{ optional($reply->created_at)->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-gray-600 text-sm">No replies yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
