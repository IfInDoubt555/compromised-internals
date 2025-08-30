<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Consent / vendor -->
    <script src="https://cmp.osano.com/QSYKFTgmsG/68c885bf-d384-489c-a092-2092f351097c/osano.js"></script>

    <!-- Set theme BEFORE CSS paints to avoid FOUC -->
    <script>
    (() => {
      let v = null;
      try { v = localStorage.getItem('ci-theme'); } catch {}
      const isDark = v === 'dark'; // default LIGHT when unset
      document.documentElement.classList.toggle('dark', isDark);
    })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Inter&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Vite Build -->
    @vite(['resources/css/app.css', 'resources/css/fade.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body
  class="antialiased text-stone-900 dark:text-stone-200
         bg-gradient-to-b from-stone-400 to-stone-500 dark:from-stone-950 dark:to-stone-900
         selection:bg-rose-500/30"
  data-events-endpoint="{{ url('/api/events') }}"
  data-feed-tpl="{{ url('/calendar/feed/{year}.ics') }}"
  data-download-tpl="{{ url('/calendar/download/{year}.ics') }}"
>
    <!-- Alpine theme store (after Alpine loads via your app.js this will initialize) -->
    <script>
      document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
          dark: document.documentElement.classList.contains('dark'),
          set(v){
            this.dark = !!v;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('ci-theme', this.dark ? 'dark' : 'light');
          },
          toggle(){ this.set(!this.dark); }
        });
      });
    </script>

    @auth
      @if (Auth::user()->profile && Auth::user()->profile->isBirthday())
        <div class="fixed top-4 right-4 bg-yellow-200 text-yellow-800 px-4 py-2 rounded shadow z-50">
            🎂 Happy Birthday, {{ Auth::user()->profile->display_name }}!
        </div>
      @endif
    @endauth

    @include('layouts.navigation')

    <!-- Global page wrapper (no hard-coded gray background anymore) -->
    <div id="theme-wrapper" class="min-h-screen">

        @isset($header)
        <<header class="bg-gradient-to-b from-slate-300 to-slate-400 dark:from-stone-950 dark:to-stone-900 shadow-sm ring-1 ring-stone-900/5 dark:ring-white/10">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <main>
            @if (session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition:leave="transition ease-in transform duration-700"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-full"
                class="max-w-7xl mx-auto px-4 py-3">
                <div class="bg-green-100 dark:bg-emerald-900/40 border border-green-400/60 dark:border-emerald-700/60 text-green-700 dark:text-emerald-200 px-4 py-3 rounded relative shadow-md" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    @if ((request()->is('shop*') || request()->is('cart')) && !request()->is('checkout*'))
      <div class="fixed bottom-4 right-4 z-50 lg:hidden">
        <a href="{{ route('shop.cart.index') }}"
           class="relative flex items-center justify-center w-16 h-16 bg-red-600 shadow-lg rounded-full hover:bg-red-700 transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-red-400">
          <!-- Cart Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.293 1.293a1 1 0 001.414 1.414L7 13zm10 0l1.293 1.293a1 1 0 01-1.414 1.414L17 13z" />
          </svg>
          @if (session('cart') && count(session('cart') ?? []) > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-600 rounded-full">
              {{ array_sum(array_column(session('cart'), 'quantity')) }}
            </span>
          @endif
        </a>
      </div>
    @endif

    @stack('scripts')

    @include('partials.footer')

    @if (
        request()->is('/') ||
        request()->is('history*') ||
        request()->is('calendar*')
    )
        <!-- Buy Me a Coffee Floating Widget (only on Home, History, Blog & Calendar) -->
        <script data-name="BMC-Widget"
            src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js"
            data-id="CompromisedInternals"
            data-description="Support me on Buy me a coffee!"
            data-message="Thank you for visiting. Any support is appreciated."
            data-color="#FF5F5F"
            data-position="Right"
            data-x_margin="18"
            data-y_margin="18">
        </script>
    @endif
</body>
</html>