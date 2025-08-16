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

    {{-- Reply form --}}
    <div class="mt-8">
        @auth
            <form action="{{ route('replies.store', $thread->slug) }}" method="POST" class="space-y-2">
                @csrf
                <textarea name="body" rows="4" required
                          placeholder="Write a reply…"
                          class="w-full rounded border border-gray-300 bg-white p-3 focus:outline-none focus:ring">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end">
                    <button class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">
                        Post Reply
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-gray-600">
                Please <a href="{{ route('login') }}" class="text-red-600 underline">log in</a> to reply.
            </p>
        @endauth
    </div>

    {{-- Replies --}}
    <section class="mt-10">
        <h2 class="mb-4 text-xl font-semibold">Replies ({{ $thread->replies->count() }})</h2>

        <div class="space-y-4">
            @forelse($thread->replies as $reply)
                <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow"
                     x-data="{ editing:false, body:`{{ addslashes($reply->body) }}` }">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            <span class="font-semibold text-gray-700">
                                {{ $reply->user->display_name ?? $reply->user->name ?? 'Unknown' }}
                            </span>
                            • {{ optional($reply->created_at)->diffForHumans() }}
                        </p>

                        @if(auth()->check() && auth()->id() === $reply->user_id)
                        <div class="flex gap-3 text-xs">
                            <button @click="editing = !editing" class="text-yellow-600 hover:underline">✏️ Edit</button>
                            <form action="{{ route('replies.destroy', $reply) }}" method="POST"
                                  onsubmit="return confirm('Delete this reply?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">🗑️ Delete</button>
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Body / edit field --}}
                    <div class="mt-2">
                        <p x-show="!editing" class="text-sm text-gray-800 whitespace-pre-line" x-text="body"></p>

                        @if(auth()->check() && auth()->id() === $reply->user_id)
                        <form x-show="editing" method="POST" action="{{ route('replies.update', $reply) }}" class="mt-2 space-y-2">
                            @csrf
                            @method('PATCH')
                            <textarea name="body" x-model="body" rows="4"
                                      class="w-full rounded border border-gray-300 bg-white p-2" required></textarea>
                            <div class="flex gap-2">
                                <button class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600">✔️ Update</button>
                                <button type="button" @click="editing=false" class="text-gray-500">Cancel</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-600 text-sm">No replies yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection