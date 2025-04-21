@extends('layouts.app')

@section('content')
    @if (auth()->check() && auth()->id() === $user->id)
        <div class="text-center mb-4">
            <a href="{{ route('profile.edit') }}" class="inline-block px-4 py-2 mt-6 bg-yellow-400 text-black text-sm font-semibold rounded-full shadow      hover:bg-yellow-500 transition">
                ✏️ Edit Your Profile
            </a>
        </div>
    @endif
<div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded-xl shadow text-center">
    <x-user-avatar :user="$user" size="w-24 h-24" class="mx-auto mb-4" />

    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>

    @if ($user->profile)
        <div class="mt-4 text-left space-y-2 text-sm text-gray-700">
            @if ($user->profile->display_name)
                <p><span class="font-semibold">Display Name:</span> {{ $user->profile->display_name }}</p>
            @endif
            @if ($user->profile->location)
                <p><span class="font-semibold">Location:</span> {{ $user->profile->location }}</p>
            @endif
            @if ($user->profile->rally_fan_since)
                <p><span class="font-semibold">Rally Fan Since:</span> {{ $user->profile->rally_fan_since }}</p>
            @endif
            @if ($user->profile->birthday)
                <p><span class="font-semibold">Birthday:</span> {{ \Carbon\Carbon::parse($user->profile->birthday)->format('F j, Y') }}</p>
            @endif
            @if ($user->profile->favorite_driver)
                <p><span class="font-semibold">Favorite Driver:</span> {{ $user->profile->favorite_driver }}</p>
            @endif
            @if ($user->profile->favorite_car)
                <p><span class="font-semibold">Favorite Car:</span> {{ $user->profile->favorite_car }}</p>
            @endif
            @if ($user->profile->bio)
                <p class="mt-4"><span class="font-semibold">About Me:</span><br> {{ $user->profile->bio }}</p>
            @endif
        </div>
    @else
        <p class="text-gray-600 mt-4">No profile details available yet.</p>
    @endif
</div>
@endsection
 