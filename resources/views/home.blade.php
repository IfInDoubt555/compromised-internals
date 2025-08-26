@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Compromised Internals | Rally Racing News, History & Events',
        'description' => 'Your one-stop hub for rally racing: daily news, interactive history, upcoming event calendar, driver & car profiles, and community insights.',
        'url'         => url('/'),
        'image'        => asset('images/ci-og.png'),
        'favicon'     => asset('favicon.png'),
    ];
@endphp

@push('head')

    <link rel="icon" href="{{ $seo['favicon'] }}" type="image/png" />

    <!-- Primary Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}" />

    <!-- Open Graph -->
    <meta property="og:type"        content="website" />
    <meta property="og:site_name"   content="Compromised Internals" />
    <meta property="og:url"         content="{{ $seo['url'] }}" />
    <meta property="og:title"       content="{{ $seo['title'] }}" />
    <meta property="og:description" content="{{ $seo['description'] }}" />
    <meta property="og:image"       content="{{ $seo['image'] }}" />

    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:url"         content="{{ $seo['url'] }}" />
    <meta name="twitter:title"       content="{{ $seo['title'] }}" />
    <meta name="twitter:description" content="{{ $seo['description'] }}" />
    <meta name="twitter:image"       content="{{ $seo['image'] }}" />

    <!-- Schema.org: Site Search Box -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "url": "{{ $seo['url'] }}",
      "name": "Compromised Internals",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/blog') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-400 text-white font-sans">

    <!-- Welcome Message -->
    <div class="bg-gray-700 py-10 text-center shadow mb-4">
        <h1 class="text-3xl font-bold">Welcome to Compromised Internals</h1>
        <p class="mt-2 text-white">Your one-stop hub for everything rally – news, history, events, and more.</p>
        <div class="text-center mt-2">
            <a href="{{ route('contact') }}"
                class="inline-block text-sm text-blue-600 hover:underline bg-yellow-100 border border-yellow-400 px-3 py-1 rounded shadow-md">
                🛠️ Click here to leave feedback during testing
            </a>
        </div>

        <div class="text-center mt-2">
            <a href="{{ route('security.policy') }}"
                class="inline-block text-sm px-4 py-2 rounded-md bg-blue-100 text-blue-700 font-medium hover:bg-blue-200 transition">
                🛡️ Report a Security Issue
            </a>
        </div>
    </div>

    <!-- History Highlights -->
    <section class="max-w-6xl mx-auto px-6 mb-8">
        <h2 class="text-2xl font-bold mb-2 text-black text-center">📚 History Highlights</h2>
        <p class="text-center text-black mb-6">Explore the comprehensive history for rally dating back to 1960. I will be working on expanding further as time goes on.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Event --}}
            @if($event)
            <div class="card bg-white rounded-lg shadow-md overflow-hidden flex flex-col items-center p-4">
                <h2 class="text-xl text-black font-bold mb-2 text-center">{{ $event['title'] ?? 'Untitled' }}</h2>
                <p class="text-gray-600 mb-4 text-center">{{ $event['bio'] ?? 'No description available.' }}</p>
                <a href="{{ route('history.show', ['tab' => 'events', 'decade' => $event['decade'], 'id' => $event['id']]) }}"
                    class="mt-auto text-blue-600 hover:underline">View Event</a>
            </div>
            @endif

            {{-- Car --}}
            @if($car)
            <div class="card bg-white rounded-lg shadow-md overflow-hidden flex flex-col items-center p-4">
                <h2 class="text-xl text-black font-bold mb-2 text-center">{{ $car['name'] ?? 'Unnamed Car' }}</h2>
                <p class="text-gray-600 mb-4 text-center">{{ $car['bio'] ?? 'No description available.' }}</p>
                <a href="{{ route('history.show', ['tab' => 'cars', 'decade' => $car['decade'], 'id' => $car['id']]) }}"
                    class="mt-auto text-blue-600 hover:underline">View Car</a>
            </div>
            @endif

            {{-- Driver --}}
            @if($driver)
            <div class="card bg-white rounded-lg shadow-md overflow-hidden flex flex-col items-center p-4">
                <h2 class="text-xl text-black font-bold mb-2 text-center">{{ $driver['name'] ?? 'Unnamed Driver' }}</h2>
                <p class="text-gray-600 mb-4 text-center">{{ $driver['bio'] ?? 'No description available.' }}</p>
                <a href="{{ route('history.show', ['tab' => 'drivers', 'decade' => $driver['decade'], 'id' => $driver['id']]) }}"
                    class="mt-auto text-blue-600 hover:underline">View Driver</a>
            </div>
            @endif
        </div>
    </section>

    {{-- BLOG: Media List Rows --}}
<section class="max-w-6xl mx-auto px-6 mt-16">
  <h2 class="text-2xl text-black font-bold mb-2 text-center">📰 Latest Blog Posts</h2>
  <p class="text-black text-center mb-6">Fresh posts from Compromised Internals.</p>

  <ul class="divide-y divide-gray-300/60">
    @foreach($posts as $post)
      <li class="py-5">
        <a href="{{ route('posts.show', $post->slug) }}" class="grid sm:grid-cols-[160px_1fr] gap-5 items-center group">
          <div class="aspect-[16/10] w-full sm:w-40 overflow-hidden rounded-lg">
            <img
              src="{{ $post->image_path && Storage::disk('public')->exists($post->image_path) ? Storage::url($post->image_path) : asset('images/default-post.png') }}"
              alt="{{ $post->title }}"
              class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
          </div>
          <div>
            <div class="text-xs text-gray-600">
              <span class="font-medium text-gray-800">{{ $post->user?->name ?? 'Unknown' }}</span>
              <span>•</span>
              <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('M j, Y') }}</time>
            </div>
            <h3 class="mt-1 font-orbitron text-xl font-bold text-gray-900 group-hover:underline">{{ $post->title }}</h3>
            <p class="mt-1 text-gray-700 line-clamp-2">{{ $post->excerpt }}</p>
          </div>
        </a>
      </li>
    @endforeach
  </ul>
</section>

</div>
@endsection