@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h1 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-4">Restricted Access</h1>
        <p class="text-sm text-center text-gray-600 dark:text-gray-300 mb-6">
            This site is currently in private testing. Please enter the access password to continue.
        </p>

        @if ($errors->any())
        <div class="mb-4 text-red-600 text-center">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('gatekeeper.submit') }}">
            @csrf
            <input
                type="password"
                name="password"
                class="w-full px-4 py-2 border rounded-lg mb-4 dark:bg-gray-700 dark:text-white"
                placeholder="Access Password"
                required>
            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Enter
            </button>
        </form>
    </div>
</div>
@endsection