@extends('layouts.app')

@section('content')

@if (auth()->check() && auth()->id() === $user->id)
        <div class="text-center mb-4">
            <a href="{{ route('profile.edit') }}" class="inline-block px-4 py-2 mt-6 bg-yellow-400 text-black text-sm font-semibold rounded-full shadow      hover:bg-yellow-500 transition">
                ✏️ Edit Your Profile
            </a>
        </div>
    @endif
<div class="max-w-4xl mx-auto mt-6 p-8 bg-white shadow-xl rounded-2xl">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
        <div class="w-40 h-40 rounded-full overflow-hidden bg-gray-100 border shadow">
            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-avatar.png') }}"
                 alt="{{ $user->name }}'s avatar"
                 class="object-cover w-full h-full">
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-bold text-center md:text-left mb-4">{{ $user->name }}</h1>

            <div class="grid md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                @if ($user->profile->display_name)
                    <div><span class="font-semibold">Display Name:</span> {{ $user->profile->display_name }}</div>
                @endif

                @if ($user->profile->location)
                    <div><span class="font-semibold">Location:</span> {{ $user->profile->location }}</div>
                @endif

                @if ($user->profile->rally_fan_since)
                    <div><span class="font-semibold">Rally Fan Since:</span> {{ $user->profile->rally_fan_since }}</div>
                @endif

                @if ($user->profile->birthday)
                    <div><span class="font-semibold">Birthday:</span> {{ \Carbon\Carbon::parse($user->profile->birthday)->format('F j, Y') }}</div>
                @endif

                @if ($user->profile->favorite_driver)
                    <div><span class="font-semibold">Favorite Driver:</span> {{ $user->profile->favorite_driver }}</div>
                @endif

                @if ($user->profile->favorite_car)
                    <div><span class="font-semibold">Favorite Car:</span> {{ $user->profile->favorite_car }}</div>
                @endif
            </div>

            @if ($user->profile->bio)
                <div class="mt-6">
                    <h2 class="text-lg font-bold mb-1">🛠 About Me</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $user->profile->bio }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
