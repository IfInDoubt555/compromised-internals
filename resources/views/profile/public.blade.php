@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-12 bg-white p-6 rounded-xl shadow text-center">
    <x-user-avatar :user="$user" size="w-24 h-24" class="mx-auto mb-4" />
    @if (auth()->check() && auth()->id() === $user->id)
        <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline text-sm">
            ✏️ Edit Your Profile
        </a>
    @endif

    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
    <p class="text-gray-600">More user info coming soon...</p>
</div>
@endsection