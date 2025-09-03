<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark" id="meta-color-scheme">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- View-specific preloads (e.g., hero image) --}}
    @stack('preload')

    <!-- Consent / vendor - load at idle to avoid blocking FCP/LCP -->
    <script>
      (function loadOsanoWhenIdle() {
        var boot = function () {
          var s = document.createElement('script');
          s.src = 'https://cmp.osano.com/QSYKFTgmsG/68c885bf-d384-489c-a092-2092f351097c/osano.js';
          s.defer = true;
          document.head.appendChild(s);
        };
        if ('requestIdleCallback' in window) {
          requestIdleCallback(boot, { timeout: 2000 });
        } else {
          window.addEventListener('load', function () { setTimeout(boot, 0); }, { once: true });
        }
      })();
    </script>

    <!-- Set theme BEFORE CSS paints to avoid FOUC -->
    <script>
    (() => {
      const STORAGE_KEY = 'ci-theme';  // 'light' | 'dark' | 'system'
      const mql = window.matchMedia('(prefers-color-scheme: dark)');

      const getStored = () => {
        try { return localStorage.getItem(STORAGE_KEY); } catch { return null; }
      };

      const setMeta = (isDark) => {
        const el = document.getElementById('meta-color-scheme');
        if (el) el.content = isDark ? 'dark light' : 'light dark';
      };

      const apply = (mode, { notify = false } = {}) => {
        const isDark = mode === 'dark' || (mode !== 'light' && mql.matches);
        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.dataset.theme = isDark ? 'dark' : 'light';
        setMeta(isDark);
        if (notify) {
          document.dispatchEvent(new CustomEvent('ci-theme:changed', { detail: { mode, dark: isDark } }));
        }
      };

      // default to "system" if nothing stored
      let mode = getStored() || 'system';
      apply(mode);

      // keep in sync with OS when mode is "system"
      (mql.addEventListener ? mql.addEventListener('change', onChange) : mql.addListener(onChange));
      function onChange() {
        if ((getStored() || 'system') === 'system') apply('system', { notify: true });
      }

      // expose helpers for Alpine / UI
      window.CI_THEME = {
        getMode: () => getStored() || 'system',
        setMode: (newMode) => {
          try { localStorage.setItem(STORAGE_KEY, newMode); } catch {}
          apply(newMode, { notify: true });
        },
        toggle: () => {
          const cur = getStored() || 'system';
          const next = cur === 'light' ? 'dark' : cur === 'dark' ? 'system' : 'light';
          try { localStorage.setItem(STORAGE_KEY, next); } catch {}
          apply(next, { notify: true });
          return next;
        }
      };
    })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Vite Build (global) -->
    @vite(['resources/css/app.css', 'resources/css/fade.css', 'resources/js/app.js'])

    {{-- Page-scoped CSS (e.g., calendar) --}}
    @stack('page-css')

    {{-- Anything else pages push into <head> --}}
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
    {{-- Anchor target for back-to-top and scroll controls --}}
    <div id="top"></div>

    @auth
      @if (Auth::user()->profile && Auth::user()->profile->isBirthday())
        <div class="fixed top-4 right-4 bg-yellow-200 text-yellow-800 px-4 py-2 rounded shadow z-50">
            🎂 Happy Birthday, {{ Auth::user()->profile->display_name }}!
        </div>
      @endif
    @endauth

    {{-- Main site navigation --}}
    @include('layouts.navigation')

    <!-- Global page wrapper -->
    <div id="theme-wrapper" class="min-h-screen">
        @isset($header)
        <header class="bg-gradient-to-b from-slate-300 to-slate-400 dark:from-stone-950 dark:to-stone-900 shadow-sm ring-1 ring-stone-900/5 dark:ring-white/10">
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

    {{-- Scripts pushed by views --}}
    @stack('scripts')

    @php
      $isStreamingContext = request()->is('travel*') || request()->is('calendar*') || request()->is('events*');

      // Show BMAC on home/history/calendar and blog pages EXCEPT blog.index
      $showBmac = (
          request()->is('/') ||
          request()->is('history*') ||
          request()->is('calendar*') ||
          request()->is('blog*')
      ) && !request()->routeIs('blog.index');
    @endphp

    @if ($isStreamingContext)
      <x-affiliate.nordvpn-footer-stream-bar />
    @else
      <x-affiliate.nordvpn-footer-bar />
    @endif

    @include('partials.footer')

    @if ($showBmac)
        <!-- Buy Me a Coffee Floating Widget -->
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

    {{-- Page-tail injections (fixed UI, modals, lazy UI like scroll controls) --}}
    @stack('after-body')
</body>
</html>