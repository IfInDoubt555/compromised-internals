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
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-xl font-semibold">{{ $post->title }}</h2>
                        <span class="text-sm text-gray-400">
                            by {{ $post->user->name ?? 'Unknown' }}
                        </span>
                    </div>

                    <p class="text-gray-300 mb-4 line-clamp-3">
                        {{ Str::limit($post->body, 300) }}
                    </p>

                    <div class="flex space-x-4">
                        <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                            @csrf
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                                ✅ Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.posts.reject', $post) }}">
                            @csrf
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow">
                                ❌ Reject
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
