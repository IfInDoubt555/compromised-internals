@extends('layouts.app')

@section('head')
  <meta name="robots" content="noindex,nofollow">
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
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

  <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>

  @if($post->image_path ?? false)
    <img src="{{ asset('storage/'.$post->image_path) }}" alt="" class="rounded-lg mb-6">
  @endif

  <article class="prose dark:prose-invert">
    {{-- If you convert Markdown to HTML elsewhere, render that here. Otherwise fallback to escaped text. --}}
    {!! $post->body_html !!}
  </article>
</div>
@endsection