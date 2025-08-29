@extends('layouts.app')

@section('content')

@if (auth()->check() && auth()->id() === $user->id)
<div class="text-center mb-4">
    <a href="{{ route('profile.edit') }}"
       class="inline-block px-4 py-2 mt-6 bg-yellow-400 text-black text-sm font-semibold rounded-full shadow hover:bg-yellow-500 transition
              dark:bg-amber-400/90 dark:text-stone-900 dark:hover:bg-amber-400 dark:ring-1 dark:ring-white/10">
        ✏️ Edit Your Profile
    </a>
</div>
@endif

<div class="max-w-4xl mx-auto mt-6 p-8 bg-white shadow-xl rounded-2xl ring-1 ring-black/5
            dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
        <div class="w-40 h-40 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shadow
                    dark:bg-stone-800/60 dark:border-white/10 dark:ring-1 dark:ring-white/10">
            @if ($user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                     alt="{{ $user->name }}'s avatar"
                     class="object-cover w-full h-full" />
            @else
                <img src="{{ asset('images/default-avatar.png') }}"
                     alt="Default avatar"
                     class="object-cover w-full h-full" />
            @endif
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-bold text-center md:text-left mb-4 flex flex-wrap items-center gap-2">
                {{ $user->name }}

                @if ($user->profile && $user->profile->rally_role)
                <span class="text-sm font-semibold px-3 py-1 rounded-full shadow
                             bg-blue-100 text-blue-800
                             dark:bg-blue-900/30 dark:text-blue-300">
                    {{ $user->profile->rally_role }}
                </span>
                @endif
            </h1>

            @if ($user->profile)
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

                @if ($user->profile->favorite_driver)
                <div><span class="font-semibold">Favorite Driver:</span> {{ $user->profile->favorite_driver }}</div>
                @endif

                @if ($user->profile->favorite_car)
                <div><span class="font-semibold">Favorite Car:</span> {{ $user->profile->favorite_car }}</div>
                @endif
            </div>

            @if ($user->profile->bio)
            <div class="mt-6">
                <h2 class="text-lg font-bold mb-1 dark:text-stone-100">🛠 About Me</h2>
                <p class="text-gray-700 leading-relaxed dark:text-stone-300">{{ $user->profile->bio }}</p>
            </div>
            @endif
            @else
            <p class="text-sm text-gray-500 mt-4 dark:text-stone-400">This user hasn't filled out their profile yet.</p>
            @endif
        </div>
    </div>
</div>

@endsection