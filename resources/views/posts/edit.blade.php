@extends('layouts.app')

@section('content')
  <x-post-form
    :action="route('posts.update', $post)"
    method="PATCH"
    title="Edit Post ✏️"
    submitLabel="Update Post"
    :model="$post"
    :boards="$boards ?? \App\Models\Board::orderBy('position')->get()" />
@endsection