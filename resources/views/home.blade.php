@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-400 text-white font-sans">

    <!-- Welcome Message -->
    <div class="bg-gray-700 py-10 text-center shadow mb-4">
        <h1 class="text-3xl font-bold">Welcome to Compromised Internals</h1>
        <p class="mt-2 text-white">Your one-stop hub for everything rally – news, history, events, and more.</p>
    </div>

    <!-- History Highlights -->
    <section class="max-w-6xl mx-auto px-6 mb-12">
        <h2 class="text-2xl font-bold mb-2 text-black text-center">📚 History Highlights</h2>
        <p class="text-center text-black mb-6">Explore the comprehensive history for rally dating back to 1960. I will be working on expanding further as time goes on.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Event --}}
            @if($event)
            <div class="card bg-white rounded-lg shadow-md overflow-hidden flex flex-col items-center p-4">
                <img src="{{ asset($event['image'] ?? 'images/dont-cut.jpg') }}"
                    alt="{{ $event['title'] ?? 'Event Image' }}"
                    class="w-full h-90 object-cover mb-4 rounded"
                    onerror="this.onerror=null;this.src='/images/dont-cut.jpg';" />
                <h2 class="text-xl text-black font-bold mb-2 text-center">{{ $event['title'] ?? 'Untitled' }}</h2>
                <p class="text-gray-600 mb-4 text-center">{{ $event['bio'] ?? 'No description available.' }}</p>
                <a href="{{ route('history.show', ['tab' => 'events', 'decade' => $event['decade'], 'id' => $event['id']]) }}"
                    class="mt-auto text-blue-600 hover:underline">View Event</a>
            </div>
            @endif

            {{-- Car --}}
            @if($car)
            <div class="card bg-white rounded-lg shadow-md overflow-hidden flex flex-col items-center p-4">
                <img src="{{ asset($car['image'] ?? 'images/dont-cut.jpg.png') }}"
                    alt="{{ $car['name'] ?? 'Car Image' }}"
                    class="w-full h-90 object-cover mb-4 rounded"
                    onerror="this.onerror=null;this.src='/images/dont-cut.jpg';" />
                <h2 class="text-xl text-black font-bold mb-2 text-center">{{ $car['name'] ?? 'Unnamed Car' }}</h2>
                <p class="text-gray-600 mb-4 text-center">{{ $car['bio'] ?? 'No description available.' }}</p>
                <a href="{{ route('history.show', ['tab' => 'cars', 'decade' => $car['decade'], 'id' => $car['id']]) }}"
                    class="mt-auto text-blue-600 hover:underline">View Car</a>
            </div>
            @endif

            {{-- Driver --}}
            @if($driver)
            <div class="card bg-white rounded-lg shadow-md overflow-hidden flex flex-col items-center p-4">
                <img src="{{ asset($driver['image'] ?? 'images/dont-cut.jpg') }}"
                    alt="{{ $driver['name'] ?? 'Driver Image' }}"
                    class="w-full h-90 object-cover mb-4 rounded"
                    onerror="this.onerror=null;this.src='/images/dont-cut.jpg';" />
                <h2 class="text-xl text-black font-bold mb-2 text-center">{{ $driver['name'] ?? 'Unnamed Driver' }}</h2>
                <p class="text-gray-600 mb-4 text-center">{{ $driver['bio'] ?? 'No description available.' }}</p>
                <a href="{{ route('history.show', ['tab' => 'drivers', 'decade' => $driver['decade'], 'id' => $driver['id']]) }}"
                    class="mt-auto text-blue-600 hover:underline">View Driver</a>
            </div>
            @endif
        </div>
    </section>

    <!-- Blog Highlights Title -->
    <section class="max-w-6xl mx-auto px-6 mt-16 mb-8 text-center">
        <h2 class="text-2xl text-black font-bold mb-2">📰 Latest Blog Posts</h2>
        <p class="text-black text-sm">Catch up on the latest rally insights, news, and behind-the-scenes stories from our team.</p>
    </section>

    <!-- Blog Cards Section -->
    <section class="p-6 grid grid-cols-1 bg-gray-400 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
        @foreach($posts as $post)
        <div class="bg-white-700 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <div class="h-64 w-full flex items-center justify-center overflow-hidden rounded-t-lg bg-black/5 hover:bg-black/10 transition-colors duration-300">
                @if ($post->image_path && Storage::exists($post->image_path))
                <img src="{{ Storage::url($post->image_path) }}"
                    alt="{{ $post->title }}"
                    class="max-h-full max-w-full object-contain" />
                @else
                <img src="{{ asset('images/default-post.png') }}"
                    alt="Default Post Image"
                    class="max-h-full max-w-full object-contain" />
                @endif
            </div>

            <div class="p-4 flex flex-col bg-gray-300 flex-grow space-y-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('profile.public', $post->user->id) }}">
                            <x-user-avatar :user="$post->user" size="w-10 h-10" />
                        </a>
                        <div>
                            <p class="font-semibold text-sm">{{ $post->user?->name ?? 'Unknown Author' }}</p>
                            <p class="text-xs text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('posts.show', $post->slug) }}" class="px-4 py-2 bg-red-600 text-white font-semibold text-sm rounded hover:bg-red-700">
                        Read More
                    </a>
                </div>
                <h2 class="text-lg font-bold text-gray-900 font-orbitron">{{ $post->title }}</h2>
                <p class="text-gray-600 flex-grow font-orbitron">{{ $post->excerpt }}</p>
            </div>
        </div>
        @endforeach
    </section>
</div>
@endsection