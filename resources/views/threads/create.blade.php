@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <a href="{{ route('boards.show', $board->slug) }}" class="text-sm text-red-600 hover:underline">← {{ $board->name }}</a>
    <h1 class="mt-2 text-2xl font-bold">New Thread</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('threads.store', $board->slug) }}" method="POST" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold mb-1">Title</label>
            <input name="title" value="{{ old('title') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none focus:ring" required maxlength="140">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Body</label>
            <textarea name="body" rows="10" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none focus:ring" required>{{ old('body') }}</textarea>
        </div>
        <div class="flex justify-end">
            <button class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Post Thread</button>
        </div>
    </form>
</div>
@endsection