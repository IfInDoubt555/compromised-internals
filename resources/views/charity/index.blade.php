@extends('layouts.app')

@section('title', 'Charity Work')

@section('content')
<div class="max-w-4xl mx-auto mt-6 px-4 py-12 text-center rounded-2xl shadow
            ring-1 ring-black/5 bg-white/85
            dark:bg-stone-900/70 dark:ring-white/10">

    <h1 class="text-4xl font-bold mb-4 text-stone-900 dark:text-stone-100">
        ❤️ Rally for Mental Health
    </h1>

    <p class="text-lg mb-6 text-stone-700 dark:text-stone-300">
        At Compromised Internals, we donate a portion of all proceeds to trusted mental health organizations.
        This mission is close to our hearts — because pushing limits isn’t just for cars, it’s about people too.
    </p>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-2 text-stone-900 dark:text-stone-100">💰 Community Impact</h2>
        <p class="text-stone-600 dark:text-stone-300">
            As we grow, we’ll share running totals of donations made and the organizations we support.
        </p>
        <p class="mt-2 text-sm italic text-stone-500 dark:text-stone-400">(Tracker or chart coming soon)</p>
    </div>

    <p class="mb-4 text-stone-700 dark:text-stone-300">
        Know a great charity you'd like to see us support?
    </p>

    <a href="{{ route('contact') }}"
       class="inline-block px-6 py-2 font-semibold rounded
              bg-red-600 text-white hover:bg-red-700
              focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 focus-visible:ring-offset-2
              dark:bg-rose-600 dark:hover:bg-rose-500 dark:focus-visible:ring-rose-300 dark:focus-visible:ring-offset-stone-900">
        📬 Suggest a Charity
    </a>
</div>
@endsection