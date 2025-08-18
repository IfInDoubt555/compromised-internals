{{-- resources/views/threads/edit.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-bold">Edit Thread</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto bg-white shadow rounded-2xl p-6">
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
        <textarea name="content" rows="10" class="mt-1 w-full border rounded px-3 py-2" required>{{ old('content', $thread->content) }}</textarea>
        @error('content') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-center gap-3">
        <x-primary-button>Save Changes</x-primary-button>
        <a href="{{ route('threads.show', $thread) }}" class="text-sm underline">Cancel</a>
      </div>
    </form>
  </div>
</x-app-layout>
