@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Compromised Internals | Rally Racing News, History & Events',
        'description' => 'Your one-stop hub for rally racing: daily news, interactive history, upcoming event calendar, driver & car profiles, and community insights.',
        'url'         => url('/'),
        'image'       => asset('images/ci-og.png'),
        'favicon'     => asset('favicon.png'),
    ];
@endphp

@push('head')
    <link rel="icon" href="{{ $seo['favicon'] }}" type="image/png" />
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}" />
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
<div class="min-h-screen bg-stone-100 text-stone-900">
  <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

    {{-- HERO / QUICK ACTIONS --}}
    <section class="pt-8">
      <div class="rounded-2xl bg-gradient-to-r from-stone-900 to-stone-700 text-white p-6 sm:p-8 shadow">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div>
            <h1 class="font-orbitron text-2xl sm:text-3xl font-bold">Compromised Internals</h1>
            <p class="mt-2 text-stone-200">News, history, schedules, and deep-dive profiles for rally fans.</p>
          </div>
        </div>

        {{-- utility strip --}}
        <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
          <a href="{{ route('contact') }}" class="inline-flex items-center rounded-md bg-yellow-100/90 text-yellow-900 px-3 py-1 font-medium hover:bg-yellow-100">Leave feedback</a>
          <a href="{{ route('security.policy') }}" class="inline-flex items-center rounded-md bg-blue-100/90 text-blue-900 px-3 py-1 font-medium hover:bg-blue-100">Report a security issue</a>
        </div>
      </div>
    </section>

{{-- SPOTLIGHTS + NEXT EVENTS --}}
<section class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- History spotlights stacked --}}
  <div class="lg:col-span-2 space-y-6">

    @if($event)
      <article class="rounded-xl bg-white shadow p-6 flex flex-col">
        <h2 class="font-orbitron text-2xl font-bold text-center">{{ $event['title'] ?? 'Untitled Event' }}</h2>
        <p class="mt-4 text-stone-600 text-center">{{ $event['bio'] ?? 'No description available.' }}</p>
        <div class="mt-6 text-center">
          <a href="{{ route('history.show', ['tab' => 'events', 'decade' => $event['decade'], 'id' => $event['id']]) }}"
             class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:underline">
            Read More
          </a>
        </div>
      </article>
    @endif

    @if($car)
      <article class="rounded-xl bg-white shadow p-6 flex flex-col">
        <h2 class="font-orbitron text-2xl font-bold text-center">{{ $car['name'] ?? 'Unnamed Car' }}</h2>
        <p class="mt-4 text-stone-600 text-center">{{ $car['bio'] ?? 'No description available.' }}</p>
        <div class="mt-6 text-center">
          <a href="{{ route('history.show', ['tab' => 'cars', 'decade' => $car['decade'], 'id' => $car['id']]) }}"
             class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:underline">
            Read More
          </a>
        </div>
      </article>
    @endif

    @if($driver)
      <article class="rounded-xl bg-white shadow p-6 flex flex-col">
        <h2 class="font-orbitron text-2xl font-bold text-center">{{ $driver['name'] ?? 'Unnamed Driver' }}</h2>
        <p class="mt-4 text-stone-600 text-center">{{ $driver['bio'] ?? 'No description available.' }}</p>
        <div class="mt-6 text-center">
          <a href="{{ route('history.show', ['tab' => 'drivers', 'decade' => $driver['decade'], 'id' => $driver['id']]) }}"
             class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:underline">
            Read More
          </a>
        </div>
      </article>
    @endif

  </div>

  {{-- Next rallies (small “calendar” list) --}}
  <aside class="rounded-xl bg-white shadow p-5">
    <h3 class="font-orbitron text-lg font-bold">Next Rallies</h3>
    <ul class="mt-3 divide-y">
      @forelse($nextEvents ?? [] as $e)
        <li class="py-3">
          <div class="text-sm font-semibold">{{ $e->title }}</div>
          <div class="text-xs text-stone-600">
            <time datetime="{{ $e->start_date?->toDateString() }}">
              {{ optional($e->start_date)->format('M j') }}
              @if($e->end_date) – {{ $e->end_date->format('M j') }} @endif
            </time>
            @if(!empty($e->location)) • {{ $e->location }} @endif
          </div>
          @if(!empty($e->slug))
            <a href="{{ route('events.show', $e->slug) }}" class="text-xs font-medium text-stone-900 hover:underline mt-1 inline-block">
              Event details
            </a>
          @endif
        </li>
      @empty
        <li class="py-3 text-sm text-stone-600">No upcoming events found.</li>
      @endforelse
    </ul>
    <a href="{{ route('calendar.index') }}" class="mt-3 inline-flex items-center text-sm font-semibold text-stone-900 hover:underline">
      Open full calendar
    </a>
  </aside>
</section>

    {{-- BLOG FEATURED + LATEST --}}
    <section class="mt-12">
      <h2 class="text-center font-orbitron text-2xl font-bold">Latest From the Blog</h2>

      @php
        // Expect controller to pass a length-aware collection $posts (latest first)
        $featured = $posts->first();
        $rest     = $posts->slice(1);
      @endphp

      @if($featured)
        <article class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
          <a href="{{ route('posts.show', $featured->slug) }}" class="aspect-[16/9] w-full overflow-hidden rounded-2xl bg-stone-200">
            <img
              src="{{ $featured->image_path && Storage::disk('public')->exists($featured->image_path) ? Storage::url($featured->image_path) : asset('images/default-post.png') }}"
              alt="{{ $featured->title }}"
              class="h-full w-full object-cover" />
          </a>
          <div class="rounded-2xl bg-white p-6 shadow">
            <div class="text-xs text-stone-600">
              <span class="font-medium text-stone-800">{{ $featured->user?->name ?? 'Unknown' }}</span>
              <span>•</span>
              <time datetime="{{ $featured->created_at->toDateString() }}">{{ $featured->created_at->format('M j, Y') }}</time>
            </div>
            <h3 class="mt-2 font-orbitron text-2xl font-bold">
              <a href="{{ route('posts.show', $featured->slug) }}" class="hover:underline">{{ $featured->title }}</a>
            </h3>
            <p class="mt-2 text-stone-700 line-clamp-3">{{ $featured->excerpt }}</p>
            <a href="{{ route('posts.show', $featured->slug) }}" class="mt-4 inline-flex items-center font-semibold hover:underline">Read post</a>
          </div>
        </article>
      @endif

      <ul class="mt-8 divide-y divide-stone-200">
        @foreach($rest as $post)
          <li class="py-5">
            <a href="{{ route('posts.show', $post->slug) }}" class="grid sm:grid-cols-[160px_1fr] gap-5 items-center group">
              <div class="aspect-[16/10] w-full sm:w-40 overflow-hidden rounded-lg bg-stone-200">
                <img
                  src="{{ $post->image_path && Storage::disk('public')->exists($post->image_path) ? Storage::url($post->image_path) : asset('images/default-post.png') }}"
                  alt="{{ $post->title }}"
                  class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
              </div>
              <div>
                <div class="text-xs text-stone-600">
                  <span class="font-medium text-stone-800">{{ $post->user?->name ?? 'Unknown' }}</span>
                  <span>•</span>
                  <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('M j, Y') }}</time>
                </div>
                <h4 class="mt-1 font-orbitron text-xl font-bold text-stone-900 group-hover:underline">{{ $post->title }}</h4>
                <p class="mt-1 text-stone-700 line-clamp-2">{{ $post->excerpt }}</p>
              </div>
            </a>
          </li>
        @endforeach
      </ul>

      @if(method_exists($posts, 'links'))
        <div class="mt-6">{{ $posts->links() }}</div>
      @endif
    </section>

    {{-- FOOTER PAD --}}
    <div class="py-8"></div>
  </div>
</div>
@endsection