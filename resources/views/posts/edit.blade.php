@extends('layouts.app')

@section('content')
  <x-post-form
      :action="route('posts.update', $post)"
      :board="$post->board ?? null"
      :model="$post"
      method="PATCH"
      title="Edit Post"
      submit-label="Update Post" />
@endsection