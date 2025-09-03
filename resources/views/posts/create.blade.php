@extends('layouts.app')

@section('content')
  @php($boards = \App\Models\Board::orderBy('position')->get())

  <x-post-form
      :action="route('posts.store')"
      title="Create a New Rally Post"
      submit-label="Publish Post"
      :board="$board ?? null"
      :boards="$boards" />
@endsection