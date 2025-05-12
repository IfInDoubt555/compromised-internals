@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-center mb-6">📬 Contact Us</h1>

    @if (session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6 bg-white dark:bg-gray-400 p-6 rounded-xl shadow">
        @csrf

        <div>
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 dark:bg-gray-500" required>
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 dark:bg-gray-500" required>
            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="message" class="block font-semibold mb-1">Message</label>
            <textarea id="message" name="message" rows="6"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 dark:bg-gray-500"
                required>{{ old('message') }}</textarea>
            @error('message') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Send Message
            </button>
        </div>
    </form>
</div>
@endsection