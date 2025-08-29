<nav x-data="{ open: false }"
     class="bg-gradient-to-b from-slate-300 to-slate-400 dark:from-stone-950 dark:to-stone-900
            ring-1 ring-stone-900/5 dark:ring-white/10">

  <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-full">

    {{-- ROW 1 ─ Brand (left) + Auth → Admin → Theme (right) --}}
    <div class="flex items-center h-14">

      {{-- Brand --}}
      <a href="{{ route('home') }}"
         class="text-lg sm:text-xl font-bold text-red-600 whitespace-nowrap">
        Compromised Internals
      </a>

      {{-- Right cluster (desktop/tablet) --}}
      <div class="hidden sm:flex items-center gap-3 ml-auto">

        {{-- Auth controls --}}
        @auth
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="inline-flex items-center px-3 py-2 text-sm lg:text-base font-semibold
                             text-gray-800 dark:text-stone-200 hover:opacity-90 transition">
                {{ Auth::user()->name }}
                <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06 0L10 10.91l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 010-1.06z"
                        clip-rule="evenodd" />
                </svg>
              </button>
            </x-slot>
            <x-slot name="content">
              <x-dropdown-link href="{{ route('dashboard') }}">Dashboard</x-dropdown-link>
              <x-dropdown-link href="{{ route('profile.public', Auth::id()) }}">Profile</x-dropdown-link>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link href="{{ route('logout') }}"
                                 onclick="event.preventDefault(); this.closest('form').submit();">
                  Log Out
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        @else
          <div class="flex items-center gap-2">
            <x-nav-link href="{{ route('login') }}">Log in</x-nav-link>
            <x-nav-link href="{{ route('register') }}">Register</x-nav-link>
          </div>
        @endauth

        {{-- Admin (always left of theme toggle) --}}
        @can('access-admin')
          <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')" class="whitespace-nowrap">
            Admin Panel
          </x-nav-link>
        @endcan>

        {{-- Theme toggle (always far right) --}}
        <button
          x-data
          @click="$store.theme.toggle()"
          :aria-pressed="$store.theme.dark"
          role="switch"
          aria-label="Toggle dark mode"
          class="group relative inline-flex items-center justify-center select-none">
          <span
            class="relative w-16 h-9 rounded-full ring-1 transition-all duration-300
                   ring-stone-900/10 bg-stone-200
                   dark:ring-white/10 dark:bg-stone-700">
            <span class="absolute inset-y-0 left-1 flex items-center">
              <span x-cloak class="opacity-0 dark:opacity-60 transition-opacity duration-300 text-sky-500" aria-hidden="true">🌙</span>
            </span>
            <span class="absolute inset-y-0 right-1 flex items-center">
              <span x-cloak class="opacity-60 dark:opacity-0 transition-opacity duration-300 text-amber-400" aria-hidden="true">☀️</span>
            </span>
            <span
              class="absolute top-1 left-1 size-7 rounded-full shadow-sm ring-1 transition-all duration-300
                     ring-stone-900/10 bg-white
                     dark:ring-white/10 dark:bg-stone-800
                     flex items-center justify-center text-base"
              :class="$store.theme.dark ? 'translate-x-0' : 'translate-x-7'">
              <span x-cloak x-text="$store.theme.dark ? '🌙' : '☀️'"
                    :class="$store.theme.dark ? 'text-sky-300' : 'text-amber-400'"></span>
              <span class="sr-only" x-text="$store.theme.dark ? 'Dark mode on' : 'Light mode on'"></span>
            </span>
          </span>
        </button>

      </div>

      {{-- Hamburger (mobile only) --}}
      <button @click="open = !open"
              class="sm:hidden ml-auto p-2 text-gray-700 dark:text-stone-200 hover:bg-white/60 dark:hover:bg-white/10 rounded-md">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
          <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    {{-- Divider between rows --}}
    <div class="border-t border-black/5 dark:border-white/10"></div>

    @php
      $url = fn ($name, $fallback) =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
    @endphp

    {{-- ROW 2 ─ Centered main nav (desktop) --}}
    <div class="hidden lg:block py-2 md:py-3">
      <nav class="flex justify-center gap-2 text-sm lg:text-base">
        <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')" class="whitespace-nowrap">Home</x-nav-link>
        <x-nav-link href="{{ $url('calendar.index','/calendar') }}" :active="request()->routeIs('calendar.index')" class="whitespace-nowrap">Rally Schedule</x-nav-link>
        <x-nav-link href="{{ $url('travel.plan','/travel/plan') }}" :active="request()->routeIs('travel.plan')" class="whitespace-nowrap">Plan Your Trip</x-nav-link>
        <x-nav-link href="{{ $url('history.index','/history') }}" :active="request()->routeIs('history.*')" class="whitespace-nowrap">History</x-nav-link>
        <x-nav-link href="{{ $url('blog.index','/blog') }}" :active="request()->routeIs('blog.index')" class="whitespace-nowrap">Blog</x-nav-link>
        <x-nav-link href="{{ $url('resources','/rally-resources') }}" :active="request()->routeIs('resources')" class="whitespace-nowrap">Rally Resources</x-nav-link>
        <x-nav-link href="{{ $url('shop.index','/shop') }}" :active="request()->routeIs('shop.index') || request()->routeIs('shop.show')" class="whitespace-nowrap">Shop</x-nav-link>
        <x-nav-link href="{{ $url('charity.index','/charity') }}" :active="request()->routeIs('charity.index')" class="whitespace-nowrap">Charity Work</x-nav-link>
      </nav>
    </div>

  </div>

  {{-- MOBILE dropdown (unchanged): keep your existing block below --}}
  <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:hidden text-sm lg:text-base whitespace-nowrap">
    {{-- ... keep your current mobile menu exactly as you had it (including theme toggle in the menu) ... --}}
    @includeWhen(true, 'partials.nav-mobile') {{-- optional if you split it out --}}
  </div>
</nav>