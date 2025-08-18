@extends('layouts.app')

@section('content')
<div class="py-8">
  <div class="max-w-3xl mx-auto bg-white shadow rounded-2xl p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Thread</h2>

    <form method="POST" action="{{ route('threads.update', $thread) }}" class="space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="block text-sm font-medium">Title</label>
        <input name="title" value="{{ old('title', $thread->title) }}" class="mt-1 w-full border rounded px-3 py-2" required>
        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Content</label>
        <textarea name="body" rows="10" class="mt-1 w-full border rounded px-3 py-2" required>{{ old('body', $thread->body) }}</textarea>
        @error('body') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-center gap-3">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
        <a href="{{ route('threads.show', $thread) }}" class="text-sm underline">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection