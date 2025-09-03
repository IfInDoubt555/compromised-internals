@extends('layouts.app')

@section('content')
  <x-post-form
    :action="route('posts.store')"
    title="Create a New Rally Post"
    submitLabel="Publish Post"
    :boards="$boards ?? \App\Models\Board::orderBy('position')->get()" />
@endsection