@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8"
     x-data="{ editing:false, title:@js($thread->title), body:@js($thread->body) }">

    {{-- Thread header --}}
    <div class="mb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('boards.show', $thread->board->slug) }}"
                   class="text-sm text-red-600 hover:underline dark:text-rose-300">
                    ← {{ $thread->board->name }}
                </a>

                <h1 class="mt-2 text-3xl font-bold text-stone-900 dark:text-stone-100"
                    x-show="!editing" x-text="title"></h1>

                {{-- Inline title input (edit mode) --}}
                @can('update', $thread)
                <input x-show="editing"
                       type="text"
                       x-model="title"
                       class="mt-2 w-full rounded border border-gray-300 px-3 py-2 text-2xl font-bold
                              dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100 dark:placeholder-stone-500"
                       maxlength="160" />
                @endcan

                <p class="mt-1 text-xs text-gray-500 dark:text-stone-500">
                    by {{ $thread->user->display_name ?? $thread->user->name ?? 'Unknown' }}
                    • {{ optional($thread->created_at)->format('M j, Y') }}
                    • last activity {{ optional($thread->last_activity_at)->diffForHumans() }}
                </p>
            </div>

            {{-- ACTIONS (Edit / Delete) --}}
            @can('update', $thread)
            <div class="flex items-center gap-2 shrink-0">
                <template x-if="!editing">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('threads.edit', $thread) }}"
                           class="px-3 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                           ✏️ Full Edit
                        </a>
                        <button @click="editing=true"
                                class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            ✏️ Quick Edit
                        </button>
                        <form action="{{ route('threads.destroy', $thread) }}" method="POST"
                              onsubmit="return confirm('Delete this thread? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </template>

                {{-- Quick Edit controls --}}
                <template x-if="editing">
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('threads.update', $thread) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="title" :value="title">
                            <input type="hidden" name="body"  :value="body">
                            <input type="hidden" name="board_id" value="{{ $thread->board_id }}">
                            <button class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                ✔️ Save
                            </button>
                        </form>
                        <button @click="editing=false"
                                class="px-3 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300
                                       dark:bg-stone-800/60 dark:text-stone-100 dark:hover:bg-stone-800">
                            Cancel
                        </button>
                    </div>
                </template>
            </div>
            @endcan
        </div>
    </div>

    {{-- Thread body --}}
    <article class="prose max-w-none rounded-xl border border-gray-200 bg-white/80 p-6 shadow
                    dark:bg-stone-900/70 dark:border-white/10">
        {{-- Read mode --}}
        <div x-show="!editing" x-cloak>
            <div class="text-gray-800 dark:text-stone-200 whitespace-pre-line" x-text="body"></div>
        </div>

        {{-- Edit mode: body textarea --}}
        @can('update', $thread)
        <div x-show="editing" x-cloak>
            <textarea x-model="body" rows="10"
                      class="w-full rounded border border-gray-300 bg-white p-3 focus:outline-none focus:ring
                             dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100 dark:placeholder-stone-500"></textarea>
            <p class="mt-2 text-xs text-gray-500 dark:text-stone-500">
                Tip: Use <code>Shift+Enter</code> for new lines.
            </p>
        </div>
        @endcan
    </article>

    {{-- Reply form --}}
    <div class="mt-8">
        @auth
            <form action="{{ route('replies.store', $thread->slug) }}" method="POST" class="space-y-2">
                @csrf
                <textarea name="body" rows="4" required
                          placeholder="Write a reply…"
                          class="w-full rounded border border-gray-300 bg-white p-3 focus:outline-none focus:ring
                                 dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100 dark:placeholder-stone-500">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-sm text-red-600 dark:text-rose-300">{{ $message }}</p>
                @enderror

                <div class="flex justify-end">
                    <button class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">
                        Post Reply
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-gray-600 dark:text-stone-400">
                Please <a href="{{ route('login') }}" class="text-red-600 underline dark:text-rose-300">log in</a> to reply.
            </p>
        @endauth
    </div>

    {{-- Replies --}}
    <section class="mt-10">
        <h2 class="mb-4 text-xl font-semibold text-stone-900 dark:text-stone-100">
            Replies ({{ $thread->replies->count() }})
        </h2>

        <div class="space-y-4">
            @forelse($thread->replies as $reply)
                <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow
                            dark:bg-stone-900/70 dark:border-white/10"
                     x-data="{ editing:false, body:`{{ addslashes($reply->body) }}` }">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500 dark:text-stone-500">
                            <span class="font-semibold text-gray-700 dark:text-stone-300">
                                {{ $reply->user->display_name ?? $reply->user->name ?? 'Unknown' }}
                            </span>
                            • {{ optional($reply->created_at)->diffForHumans() }}
                        </p>

                        @if(auth()->check() && auth()->id() === $reply->user_id)
                        <div class="flex gap-3 text-xs">
                            <button @click="editing = !editing" class="text-yellow-600 hover:underline dark:text-amber-300">✏️ Edit</button>
                            <form action="{{ route('replies.destroy', $reply) }}" method="POST"
                                  onsubmit="return confirm('Delete this reply?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline dark:text-rose-300">🗑️ Delete</button>
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Body / edit field --}}
                    <div class="mt-2">
                        <p x-show="!editing"
                           class="text-sm text-gray-800 dark:text-stone-200 whitespace-pre-line"
                           x-text="body"></p>

                        @if(auth()->check() && auth()->id() === $reply->user_id)
                        <form x-show="editing" method="POST" action="{{ route('replies.update', $reply) }}" class="mt-2 space-y-2">
                            @csrf
                            @method('PATCH')
                            <textarea name="body" x-model="body" rows="4"
                                      class="w-full rounded border border-gray-300 bg-white p-2
                                             dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100" required></textarea>
                            <div class="flex gap-2">
                                <button class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600">✔️ Update</button>
                                <button type="button" @click="editing=false"
                                        class="text-gray-500 dark:text-stone-400">Cancel</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-stone-400 text-sm">No replies yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection