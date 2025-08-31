@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-8">

    {{-- Page header --}}
    <header class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-stone-100">Edit Your Profile</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-stone-400">
                Update your account details, profile info, and password.
            </p>
        </div>

        {{-- Quick link to public profile --}}
        <a href="{{ route('profile.public', auth()->id()) }}"
           class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-xl ring-1 ring-black/5
                  bg-white text-gray-800 hover:bg-gray-50
                  dark:bg-stone-900 dark:text-stone-100 dark:ring-white/10 dark:hover:bg-stone-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 5c7.633 0 11 6.5 11 6.5S19.633 18 12 18 1 11.5 1 11.5 4.367 5 12 5Zm0 2C6.86 7 3.402 11.095 3.05 11.5 3.4 11.904 6.86 16 12 16s8.6-4.096 8.95-4.5C20.6 11.095 17.14 7 12 7Zm0 2.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5Z"/>
            </svg>
            View Profile
        </a>
    </header>

    {{-- Success toast (covers profile + password updates) --}}
    @if (session('status'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 2200)"
            class="rounded-xl px-4 py-3 text-sm bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200
                   dark:bg-emerald-900/20 dark:text-emerald-200 dark:ring-emerald-900/30"
            role="status"
        >
            {{ session('status') }}
        </div>
    @endif

    <!-- Update Profile Information -->
    <section class="bg-white shadow-md rounded-lg p-6 ring-1 ring-black/5
                    dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
        @include('profile.partials.update-profile-information-form')
    </section>

    <!-- Update Password -->
    <section class="bg-white shadow-md rounded-lg p-6 ring-1 ring-black/5
                    dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
        {{-- <h2 class="text-2xl font-semibold mb-4">Update Password</h2> --}}
        @include('profile.partials.update-password-form')
    </section>

    <!-- Delete Account -->
    <section class="bg-white shadow-md rounded-lg p-6 ring-1 ring-black/5
                    dark:bg-stone-900/70 dark:ring-white/10">
        <h2 class="text-2xl font-semibold mb-4 text-red-600 dark:text-rose-400">Delete Account</h2>
        @include('profile.partials.delete-user-form')
    </section>

</div>
@endsection