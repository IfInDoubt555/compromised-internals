@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
  <h1 class="text-3xl font-bold mb-6">📝 Blog Moderation</h1>

  @if (session('success'))
    <div class="mb-4 p-4 bg-green-600 text-white font-medium rounded shadow">
      {{ session('success') }}
    </div>
  @endif

  @if ($pendingPosts->isEmpty())
    <div class="text-gray-400 italic">No posts waiting for moderation.</div>
  @else
    <div class="space-y-6">
      @foreach ($pendingPosts as $post)
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 shadow text-white">
          <div class="flex flex-col md:flex-row gap-6">
            {{-- Left: Image --}}
            @if ($post->image_path)
              <div x-data="{ open: false }" class="flex-shrink-0 cursor-pointer">
                <img
                  src="{{ $post->image_url }}"
                  @click="open = true"
                  class="h-48 w-auto rounded shadow object-cover hover:scale-105 transition duration-200"
                  alt="{{ $post->title }}"
                />

                <!-- Modal -->
                <div
                  x-show="open"
                  @click.outside="open = false"
                  class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
                  x-transition
                >
                  <img src="{{ $post->image_url }}" class="max-h-[90vh] max-w-[90vw] rounded shadow-xl" alt="{{ $post->title }}" />
                  <button
                    @click="open = false"
                    class="absolute top-4 right-4 text-white text-xl bg-black/50 rounded-full px-3 py-1 hover:bg-black"
                    aria-label="Close image"
                  >✕</button>
                </div>
              </div>
            @endif

            {{-- Right: Text & Controls --}}
            <div class="flex flex-col justify-between flex-1 space-y-4">
              <div>
                <div class="flex justify-between items-start">
                  <h2 class="text-xl font-semibold">{{ $post->title }}</h2>
                  <span class="text-sm text-gray-400">by {{ $post->user->name ?? 'Unknown' }}</span>
                </div>
                <p class="text-gray-300 mt-2 line-clamp-4">
                  {{ Str::limit(Str::of($post->body)->squish(), 400) }}
                </p>
              </div>

              <div class="flex space-x-4">
                <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                  @csrf
                  <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                    ✅ Approve
                  </button>
                </form>

                <form method="POST" action="{{ route('admin.posts.reject', $post) }}">
                  @csrf
                  <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow">
                    ❌ Reject
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection