@extends('layouts.app')

@section('head')
  <meta name="robots" content="noindex,nofollow">
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="mb-4 flex items-center justify-between">
    <span class="px-2 py-1 text-xs rounded bg-amber-500/10 text-amber-400 border border-amber-400/30">
      Draft Preview
    </span>
    <div class="flex gap-2">
      <a class="btn btn-sm" href="{{ route('admin.posts.edit', $post) }}">Edit</a>
      <form method="POST" action="{{ route('admin.publish.now', $post) }}">
        @csrf <button class="btn btn-sm">Publish now</button>
      </form>
    </div>
  </div>

  {{-- Two-up: Full article + List Card --}}
  <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-8">
    {{-- Full Article Preview --}}
    <section>
      <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>

      @if($post->image_path ?? false)
        <img src="{{ asset('storage/'.$post->image_path) }}" alt="" class="rounded-lg mb-6">
      @endif

      <article class="prose dark:prose-invert">
        {!! $post->body_html !!}
      </article>
    </section>

    {{-- List Card Preview (same as blog.index) --}}
    <aside class="lg:sticky lg:top-20 h-fit">
      <h2 class="ci-title-md mb-3">List Card Preview</h2>
      @include('partials.blog-post-card', ['post' => $post])
    </aside>
  </div>
</div>
@endsection
