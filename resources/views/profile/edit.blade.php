@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-8">

    <!-- Update Profile Information -->
    <div class="bg-white shadow-md rounded-lg p-6 ring-1 ring-black/5
                dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
        @include('profile.partials.update-profile-information-form')
    </div>

    <!-- Update Password -->
    <div class="bg-white shadow-md rounded-lg p-6 ring-1 ring-black/5
                dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
        {{-- <h2 class="text-2xl font-semibold mb-4">Update Password</h2> --}}
        @include('profile.partials.update-password-form')
    </div>

    <!-- Delete Account -->
    <div class="bg-white shadow-md rounded-lg p-6 ring-1 ring-black/5
                dark:bg-stone-900/70 dark:ring-white/10">
        <h2 class="text-2xl font-semibold mb-4 text-red-600 dark:text-rose-400">Delete Account</h2>
        @include('profile.partials.delete-user-form')
    </div>

</div>
@endsection