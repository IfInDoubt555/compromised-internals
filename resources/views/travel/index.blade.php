@extends('layouts.app')

@push('head')
    <title>Plan Your Trip | Compromised Internals</title>
    <meta name="description" content="Plan rally trips with hotels, camping, flights, and car rentals in one place.">
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header + intro --}}
    <h1 class="text-3xl font-bold mb-3">Plan Your Trip</h1>
    <p class="text-gray-700 mb-6">
        Heading to a rally? Compare <span class="font-semibold">hotels, camping, flights, and car rentals</span> in one place.
        Pick an event below or jump straight to the tools.
    </p>

    {{-- Rally quick-picks --}}
    <h3 class="text-lg font-bold mt-6">Find Travel by Rally</h3>
    <ul class="list-disc ml-6 mt-2 space-y-1">
        @forelse($items as $it)
            <li><a href="{{ $it['url'] }}" class="text-blue-600 underline">{{ $it['title'] }}</a></li>
        @empty
            <li class="text-gray-500">No upcoming events yet. Check back soon.</li>
        @endforelse
    </ul>

    {{-- Travel tools grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Hotels --}}
        <section class="rounded-lg border border-gray-200 p-5 bg-white">
            <h2 class="text-xl font-bold">Find the Best Hotels</h2>
            <p class="text-gray-600 text-sm mt-1 mb-4">
                Compare prices near rally HQ and popular stages.
            </p>
            <!-- Travelpayouts Hotel Widget Embed -->
            <div id="tp-hotels-widget" class="min-h-[220px] bg-gray-50 rounded-md flex items-center justify-center text-gray-500">
                Hotels widget will render here.
            </div>
        </section>

        {{-- Camping (Hipcamp target spot) --}}
        <section class="rounded-lg border border-gray-200 p-5 bg-white">
            <h2 class="text-xl font-bold">Camping Near the Rally</h2>
            <p class="text-gray-600 text-sm mt-1 mb-4">
                Prefer tents, cabins, or van sites close to stages? Explore nearby campgrounds.
            </p>
            <!-- Hipcamp / AvantLink widget or link list -->
            <div id="hipcamp-widget" class="min-h-[220px] bg-gray-50 rounded-md flex items-center justify-center text-gray-500">
                Hipcamp widget / links will render here.
            </div>
        </section>

        {{-- Flights --}}
        <section class="rounded-lg border border-gray-200 p-5 bg-white">
            <h2 class="text-xl font-bold">Book Flights</h2>
            <p class="text-gray-600 text-sm mt-1 mb-4">
                Fly into the closest airports for each event weekend.
            </p>
            <!-- Travelpayouts Flight Widget Embed -->
            <div id="tp-flights-widget" class="min-h-[220px] bg-gray-50 rounded-md flex items-center justify-center text-gray-500">
                Flights widget will render here.
            </div>
        </section>

        {{-- Cars --}}
        <section class="rounded-lg border border-gray-200 p-5 bg-white">
            <h2 class="text-xl font-bold">Car Rentals</h2>
            <p class="text-gray-600 text-sm mt-1 mb-4">
                Get to remote stages and service parks with ease.
            </p>
            <!-- Travelpayouts Car Rental Widget Embed -->
            <div id="tp-cars-widget" class="min-h-[220px] bg-gray-50 rounded-md flex items-center justify-center text-gray-500">
                Car rental widget will render here.
            </div>
        </section>
    </div>

    {{-- Travel tips --}}
    <section class="mt-10">
        <h2 class="text-xl font-bold mb-3">Travel Tips for Rally Fans</h2>
        <ul class="list-disc list-inside text-gray-700 space-y-1">
            <li>Book early for Monte-Carlo and Finland — hotels & camping fill fast.</li>
            <li>Consider car rentals for Portugal or Sardinia — many stages are remote.</li>
            <li>Check official event sites for shuttle options and restricted roads.</li>
        </ul>
    </section>

    {{-- Compliance / disclosure --}}
    <section class="mt-8">
        <p class="text-xs text-gray-500">
            Disclosure: Some links on this page are affiliate links. If you book through them, we may earn a small commission
            at no extra cost to you. This helps keep Compromised Internals running. Thanks for the support! ❤️
        </p>
    </section>
</div>
@endsection