@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-center mb-6">📬 Contact</h1>

    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6 bg-white dark:bg-gray-500 p-6 rounded-xl shadow">
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mt-4 mb-6 rounded shadow-md text-sm">
            <strong>Heads up!</strong> This site is in early testing. If you spot any bugs or have suggestions, please use this form to let me know!
        </div>

        @csrf

        <div>
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" id="name" name="name" value="{{ $errors->any() ? old('name') : '' }}"
                class="w-full border border-gray-300 dark:border-gray-700 rounded px-4 py-2 dark:bg-gray-300" required>
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ $errors->any() ? old('name') : '' }}"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 dark:bg-gray-300" required>
            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="message" class="block font-semibold mb-1">Message</label>
            <textarea id="message" name="message" rows="6"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 dark:bg-gray-300"
                required>{{ $errors->any() ? old('message') : '' }}</textarea>
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