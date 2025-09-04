@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-8"
     x-data="{ editing:false, title:@js($thread->title), body:@js($thread->body) }">

  @php
    $palette = [
      'slate'=>'bg-slate-500','red'=>'bg-red-500','amber'=>'bg-amber-500','green'=>'bg-green-500',
      'indigo'=>'bg-indigo-500','orange'=>'bg-orange-500','cyan'=>'bg-cyan-500','purple'=>'bg-purple-500',
      'emerald'=>'bg-emerald-500','blue'=>'bg-blue-500',
    ];
    $dot = $palette[$thread->board->color ?? 'slate'] ?? 'bg-slate-500';
  @endphp

  {{-- Page header --}}
  <div class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow p-4 sm:p-6 mb-6
              dark:bg-stone-900/70 dark:ring-white/10">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div class="min-w-0">
        <div class="flex items-center gap-2 text-sm">
          <a class="text-blue-700 hover:underline dark:text-sky-300" href="{{ route('boards.index') }}">Boards</a>
          <span class="text-stone-400">/</span>
          <a class="inline-flex items-center gap-2 hover:underline"
             href="{{ route('boards.show', $thread->board->slug) }}">
            <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
            <span class="text-stone-900 dark:text-stone-100">{{ $thread->board->name }}</span>
          </a>
        </div>

        <h1 x-show="!editing" class="mt-2 font-orbitron text-2xl sm:text-3xl font-bold tracking-tight
                     text-stone-900 dark:text-stone-100 truncate">
          {{ $thread->title }}
        </h1>

        @can('update', $thread)
          <input x-show="editing" x-cloak type="text" x-model="title" maxlength="160"
                 class="mt-2 w-full rounded-lg px-3 py-2 text-2xl font-semibold
                        bg-white ring-1 ring-black/10
                        dark:bg-stone-800/60 dark:ring-white/10 dark:text-stone-100" />
        @endcan

        <div class="mt-1 text-xs text-stone-600 dark:text-stone-400">
          <span class="font-medium text-stone-800 dark:text-stone-300">
            {{ $thread->user->display_name ?? 'Unknown' }}
          </span>
          • {{ optional($thread->created_at)->format('M j, Y') }}
          • last activity {{ optional($thread->last_activity_at)->diffForHumans() }}
        </div>
      </div>

      @can('update', $thread)
      <div class="flex items-center gap-2 shrink-0">
        <button x-show="!editing" @click="editing=true"
                class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium
                       bg-blue-600 text-white hover:bg-blue-700">
          ✏️ Edit
        </button>

        <div x-show="editing" x-cloak class="flex items-center gap-2">
          <form method="POST" action="{{ route('threads.update', $thread) }}" class="flex items-center gap-2">
            @csrf @method('PATCH')
            <input type="hidden" name="title" :value="title">
            <input type="hidden" name="body"  :value="body">
            <input type="hidden" name="board_id" value="{{ $thread->board_id }}">
            <button class="rounded-lg px-3 py-2 text-sm font-semibold
                           bg-emerald-600 text-white hover:bg-emerald-700">✔ Save</button>
          </form>
          <button @click="editing=false"
                  class="rounded-lg px-3 py-2 text-sm font-medium
                         bg-white ring-1 ring-black/10 hover:bg-stone-50
                         dark:bg-stone-800/60 dark:ring-white/10 dark:hover:bg-stone-800">
            Cancel
          </button>
        </div>

        <form action="{{ route('threads.destroy', $thread) }}" method="POST"
              onsubmit="return confirm('Delete this thread? This cannot be undone.');">
          @csrf @method('DELETE')
          <button class="rounded-lg px-3 py-2 text-sm font-semibold
                         bg-red-600 text-white hover:bg-red-700">🗑 Delete</button>
        </form>
      </div>
      @endcan
    </div>
  </div>

  {{-- Two-column layout --}}
  <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-8 items-start">

    {{-- LEFT: thread content + replies --}}
    <section class="min-w-0 space-y-8">

      {{-- Body (read or edit) --}}
      <article x-show="!editing" x-cloak
               class="prose dark:prose-invert max-w-none rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow p-5
                      prose-headings:font-semibold prose-h1:text-2xl prose-h2:text-xl prose-h3:text-lg
                      prose-p:leading-relaxed prose-ul:list-disc prose-ol:list-decimal prose-li:my-1
                      prose-img:rounded-lg prose-img:shadow-md prose-img:mx-auto prose-img:max-h-[36rem]      prose-img:cursor-zoom-in
                      dark:bg-stone-900/70 dark:ring-white/10
                      js-lightbox-scope">
        {!! $thread->body_html !!}
      </article>

      @can('update', $thread)
      <div x-show="editing" x-cloak
           class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow p-5
                  dark:bg-stone-900/70 dark:ring-white/10">
        <textarea x-model="body" rows="12"
                  class="w-full rounded-lg p-3 bg-white ring-1 ring-black/10
                         dark:bg-stone-800/60 dark:ring-white/10 dark:text-stone-100"></textarea>
        <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">Markdown supported.</p>
      </div>
      @endcan
      
      {{-- Replies (oldest → newest) --}}
      <section aria-labelledby="replies" class="space-y-4">
        <h2 id="replies" class="sr-only">Replies</h2>

        @forelse($thread->replies as $reply)
          <div class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow p-4
                      dark:bg-stone-900/70 dark:ring-white/10"
               x-data="{ editing:false, body:@js($reply->body) }">
            <div class="flex items-center justify-between gap-3">
              <p class="text-xs text-stone-500 dark:text-stone-400">
                <span class="font-semibold text-stone-800 dark:text-stone-300">
                  {{ $reply->user->display_name ?? 'Unknown' }}
                </span>
                • {{ optional($reply->created_at)->diffForHumans() }}
              </p>

              @if(auth()->check() && auth()->id() === $reply->user_id)
              <div class="flex gap-3 text-xs">
                <button @click="editing = !editing"
                        class="text-amber-600 hover:underline dark:text-amber-300">✏ Edit</button>
                <form action="{{ route('replies.destroy', $reply) }}" method="POST"
                      onsubmit="return confirm('Delete this reply?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="text-rose-600 hover:underline dark:text-rose-300">🗑 Delete</button>
                </form>
              </div>
              @endif
            </div>

            <div class="mt-2">
              <div x-show="!editing" x-cloak
                 class="prose dark:prose-invert max-w-none
                        prose-headings:font-semibold prose-h3:text-lg
                        prose-p:leading-relaxed prose-ul:list-disc prose-ol:list-decimal prose-li:my-1
                        prose-img:rounded-lg prose-img:shadow-md prose-img:mx-auto prose-img:max-h-96 prose-img:cursor-zoom-in
                        js-lightbox-scope">
              {!! $reply->body_html !!}
            </div>

              @if(auth()->check() && auth()->id() === $reply->user_id)
              <form x-show="editing" x-cloak method="POST" action="{{ route('replies.update', $reply) }}" class="mt-2 space-y-2">
                @csrf @method('PATCH')
                <textarea name="body" x-model="body" rows="4"
                          class="w-full rounded-lg p-2 bg-white ring-1 ring-black/10
                                 dark:bg-stone-800/60 dark:ring-white/10 dark:text-stone-100" required></textarea>
                <div class="flex items-center gap-2 text-sm">
                  <button class="rounded-lg px-3 py-1 bg-amber-500 text-white hover:bg-amber-600">✔ Update</button>
                  <button type="button" @click="editing=false"
                          class="text-stone-500 dark:text-stone-400">Cancel</button>
                </div>
              </form>
              @endif
            </div>
          </div>
        @empty
          <p class="text-sm text-stone-600 dark:text-stone-400">No replies yet.</p>
        @endforelse
      </section>
      {{-- Reply composer (moved to bottom, where the reply will appear) --}}
      <div class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow p-5
                  dark:bg-stone-900/70 dark:ring-white/10" id="reply">
        @auth
          <form action="{{ route('replies.store', $thread->slug) }}" method="POST" class="space-y-3" id="reply-form">
            @csrf
            <textarea name="body" rows="5" required placeholder="Write a reply…"
                      class="w-full rounded-lg p-3 bg-white ring-1 ring-black/10
                             dark:bg-stone-800/60 dark:ring-white/10 dark:text-stone-100">{{ old('body') }}</textarea>
            @error('body')
              <p class="text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
            @enderror
            <div class="flex justify-end">
              <button class="rounded-lg px-4 py-2 font-semibold
                             bg-red-600 text-white hover:bg-red-700">Post reply</button>
            </div>
          </form>
        @else
          <p class="text-sm text-stone-600 dark:text-stone-400">
            Please <a href="{{ route('login') }}" class="text-blue-700 underline dark:text-sky-300">log in</a> to reply.
          </p>
        @endauth
      </div>
    </section>

    {{-- RIGHT: rail --}}
    <aside class="space-y-4">
      <div class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow p-5
                  dark:bg-stone-900/70 dark:ring-white/10">
        <div class="flex items-center gap-2 text-sm">
          <span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
          <a href="{{ route('boards.show', $thread->board->slug) }}"
             class="font-medium hover:underline text-stone-900 dark:text-stone-100">
            {{ $thread->board->name }}
          </a>
        </div>
        @if($thread->board->description)
          <p class="mt-2 text-xs text-stone-600 dark:text-stone-400">
            {{ $thread->board->description }}
          </p>
        @endif
        <div class="mt-3 flex items-center gap-3 text-xs text-stone-600 dark:text-stone-400">
          <span>{{ $thread->board->threads_count ?? $thread->board->threads()->count() }} threads</span>
          <span aria-hidden="true">•</span>
          <span>{{ $thread->replies->count() }} replies</span>
        </div>
        <a href="{{ route('threads.create', $thread->board->slug) }}"
           class="mt-4 inline-flex w-full justify-center rounded-lg px-3 py-2 text-sm font-semibold
                  bg-red-600 text-white hover:bg-red-700">New thread</a>
      </div>

      @if(($relatedPosts ?? collect())->isNotEmpty())
        <div class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow p-5
                    dark:bg-stone-900/70 dark:ring-white/10">
          <h3 class="text-sm font-semibold text-stone-900 dark:text-stone-100 mb-2">Featured posts</h3>
          <ul class="space-y-2">
            @foreach($relatedPosts as $p)
              <li>
                <a href="{{ route('blog.show', $p->slug) }}"
                   class="block rounded-md px-3 py-2 hover:bg-stone-50
                          dark:hover:bg-stone-800/50">
                  <p class="font-medium line-clamp-2 text-stone-900 dark:text-stone-100">{{ $p->title }}</p>
                  <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">{{ $p->created_at->format('M j, Y') }}</p>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    </aside>

  </div>
</div>
@endsection