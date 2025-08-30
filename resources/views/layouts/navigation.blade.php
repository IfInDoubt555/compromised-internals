<nav x-data="{ open: false }"
     class="relative z-50 bg-white/80 border-b border-black/5
            dark:bg-stone-950/70 dark:border-white/10
            supports-[backdrop-filter]:backdrop-blur">
  <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-full">

    {{-- ===================== ROW 1: Logo | Admin Panel | Auth + Theme ===================== --}}
    <div class="grid grid-cols-3 items-center h-16">
      {{-- Left: Logo --}}
      <div class="justify-self-start flex items-center space-x-2 whitespace-nowrap">
        <a href="{{ route('home') }}" class="text-xl font-bold text-red-600 dark:text-red-400 whitespace-nowrap">
            Compromised Internals
        </a>
      </div>

      {{-- Center: Admin Panel (only if allowed) --}}
      <div class="justify-self-center">
        @can('access-admin')
          <a href="{{ route('admin.dashboard') }}"
             class="inline-block rounded px-2 py-1 text-sm font-semibold text-blue-700 dark:text-blue-400 hover:underline">
            Admin Panel
          </a>
        @endcan
      </div>

      {{-- Right: Auth controls + Theme toggle (+ hamburger on mobile) --}}
      <div class="justify-self-end flex items-center gap-3 whitespace-nowrap">
        @auth
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="hidden sm:inline-flex items-center px-3 py-2 text-sm lg:text-lg font-semibold text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white transition whitespace-nowrap">
                {{ Auth::user()->name }}
                <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06 0L10 10.91l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link href="{{ route('dashboard') }}">Dashboard</x-dropdown-link>
              <x-dropdown-link href="{{ route('profile.public', Auth::id()) }}">Profile</x-dropdown-link>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                  Log Out
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        @else
          <div class="hidden sm:flex items-center space-x-2 text-sm lg:text-base">
            <x-nav-link href="{{ route('login') }}">Log in</x-nav-link>
            <x-nav-link href="{{ route('register') }}">Register</x-nav-link>
          </div>
        @endauth

        {{-- Theme Switch (pill) --}}
        <button
            type="button"
            @click="$store.theme.toggle()"
            :aria-pressed="$store.theme.dark.toString()"
            role="switch"
            aria-label="Toggle dark mode"
            class="group relative inline-flex items-center justify-center w-16 h-9 rounded-full ring-1 transition-all duration-300
                   ring-stone-900/10 bg-stone-200
                   dark:ring-white/10 dark:bg-stone-700 select-none"
        >
            <!-- Moon (dark) -->
            <span class="absolute inset-y-0 left-1 flex items-center">
                <span x-cloak class="opacity-0 dark:opacity-60 transition-opacity duration-300 text-sky-500" aria-hidden="true">
                    🌙
                </span>
            </span>
        
            <!-- Sun (light) -->
            <span class="absolute inset-y-0 right-1 flex items-center">
                <span x-cloak class="opacity-60 dark:opacity-0 transition-opacity duration-300 text-amber-400" aria-hidden="true">
                    ☀️
                </span>
            </span>
        
            <!-- Toggle knob -->
            <span
                class="absolute top-1 left-1 size-7 rounded-full shadow-sm ring-1 transition-all duration-300
                       ring-stone-900/10 bg-white
                       dark:ring-white/10 dark:bg-stone-800
                       flex items-center justify-center text-base"
                :class="$store.theme.dark ? 'translate-x-0' : 'translate-x-7'"
            >
                <span x-cloak x-text="$store.theme.dark ? '🌙' : '☀️'"
                      :class="$store.theme.dark ? 'text-sky-300' : 'text-amber-400'">
                </span>
                <span class="sr-only" x-text="$store.theme.dark ? 'Dark mode on' : 'Light mode on'"></span>
            </span>
        </button>

        {{-- Hamburger (mobile only) --}}
        <button @click="open = ! open"
                class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-zinc-800 rounded-md focus:outline-none">
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
    </div>

    {{-- Route fallback helper (kept from your file) --}}
    @php
      $url = function ($name, $fallback) {
          return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
      };
    @endphp

    {{-- ===================== ROW 2: Primary Nav (desktop) ===================== --}}
<div class="hidden lg:block text-gray-800 dark:text-gray-200">
      <div class="py-2">
         <div class="flex items-center justify-center gap-x-6 gap-y-2 text-sm lg:text-base whitespace-nowrap overflow-x-auto no-scrollbar">
          <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')" class="whitespace-nowrap">Home</x-nav-link>

          <x-nav-link href="{{ $url('calendar.index','/calendar') }}"
                      :active="request()->routeIs('calendar.index')"
                      class="whitespace-nowrap">Rally Schedule</x-nav-link>

          <x-nav-link href="{{ $url('travel.plan','/travel/plan') }}"
                      :active="request()->routeIs('travel.plan')"
                      class="whitespace-nowrap">Plan Your Trip</x-nav-link>

          <x-nav-link href="{{ $url('history.index','/history') }}"
                      :active="request()->routeIs('history.*')"
                      class="whitespace-nowrap">History</x-nav-link>

          <x-nav-link href="{{ $url('blog.index','/blog') }}"
                      :active="request()->routeIs('blog.index')"
                      class="whitespace-nowrap">Blog</x-nav-link>

          <x-nav-link href="{{ $url('resources','/rally-resources') }}"
                      :active="request()->routeIs('resources')"
                      class="whitespace-nowrap">Rally Resources</x-nav-link>

          <x-nav-link href="{{ $url('shop.index','/shop') }}"
                      :active="request()->routeIs('shop.index') || request()->routeIs('shop.show')"
                      class="whitespace-nowrap">Shop</x-nav-link>

          <x-nav-link href="{{ $url('charity.index','/charity') }}"
                      :active="request()->routeIs('charity.index')"
                      class="whitespace-nowrap">Charity Work</x-nav-link>
        </div>
      </div>
    </div>
  </div>

  {{-- ===================== Mobile Dropdown (unchanged behavior) ===================== --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:hidden text-sm lg:text-base whitespace-nowrap bg-white/95 dark:bg-zinc-900/95 text-gray-800 dark:text-gray-200">    <div class="pt-2 pb-3 space-y-1">
      @auth
        <div class="px-4 border-t border-gray-200 dark:border-zinc-800 pt-4 pb-1">
          <div class="font-medium text-base text-gray-800 whitespace-nowrap">{{ Auth::user()->name }}</div>
          <div class="font-medium text-sm text-gray-500 whitespace-nowrap">{{ Auth::user()->email }}</div>
        </div>

        <x-responsive-nav-link href="{{ route('profile.public', Auth::id()) }}">Profile</x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('dashboard') }}">Dashboard</x-responsive-nav-link>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
            Log Out
          </x-responsive-nav-link>
        </form>
      @else
        <x-responsive-nav-link href="{{ route('login') }}">Log in</x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('register') }}">Register</x-responsive-nav-link>
      @endauth

      <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">Home</x-responsive-nav-link>

      <x-responsive-nav-link href="{{ $url('calendar.index','/calendar') }}"
                             :active="request()->routeIs('calendar.index')">Rally Schedule</x-responsive-nav-link>

      <x-responsive-nav-link href="{{ $url('travel.plan','/travel/plan') }}"
                             :active="request()->routeIs('travel.plan')">Plan Your Trip</x-responsive-nav-link>

      <x-responsive-nav-link href="{{ $url('history.index','/history') }}"
                             :active="request()->routeIs('history.*')">History</x-responsive-nav-link>

      <x-responsive-nav-link href="{{ $url('blog.index','/blog') }}"
                             :active="request()->routeIs('blog.index')">Blog</x-responsive-nav-link>

      <x-responsive-nav-link href="{{ $url('resources','/rally-resources') }}"
                             :active="request()->routeIs('resources')">Rally Resources</x-responsive-nav-link>

      <x-responsive-nav-link href="{{ $url('shop.index','/shop') }}"
                             :active="request()->routeIs('shop.index') || request()->routeIs('shop.show')">Shop</x-responsive-nav-link>

      <x-responsive-nav-link href="{{ $url('charity.index','/charity') }}"
                             :active="request()->routeIs('charity.index')">Charity Work</x-responsive-nav-link>

      @can('access-admin')
        <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')">
          Admin Panel
        </x-responsive-nav-link>
      @endcan

      {{-- Theme switch (mobile) --}}
      <div class="px-4 py-2">
        <button
          x-data
          @click="$store.theme.toggle()"
          :aria-pressed="$store.theme.dark"
          role="switch"
          aria-label="Toggle dark mode"
          class="group relative inline-flex items-center justify-center select-none"
        >
          <span
            class="relative w-14 h-8 rounded-full ring-1 transition-all duration-300
                   ring-stone-900/10 bg-stone-200
                   dark:ring-white/10 dark:bg-stone-700"
          >
            <span class="absolute inset-y-0 left-1 flex items-center">
              <span x-cloak class="opacity-0 dark:opacity-60 transition-opacity duration-300 text-sky-500" aria-hidden="true">🌙</span>
            </span>
            <span class="absolute inset-y-0 right-1 flex items-center">
              <span x-cloak class="opacity-60 dark:opacity-0 transition-opacity duration-300 text-amber-400" aria-hidden="true">☀️</span>
            </span>
            <span
              class="absolute top-1 left-1 size-6 rounded-full shadow-sm ring-1 transition-all duration-300
                     ring-stone-900/10 bg-white
                     dark:ring-white/10 dark:bg-stone-800
                     flex items-center justify-center text-sm"
              :class="$store.theme.dark ? 'translate-x-0' : 'translate-x-6'"
            >
              <span x-cloak x-text="$store.theme.dark ? '🌙' : '☀️'"
                    :class="$store.theme.dark ? 'text-sky-300' : 'text-amber-400'"></span>
              <span class="sr-only" x-text="$store.theme.dark ? 'Dark mode on' : 'Light mode on'"></span>
            </span>
          </span>
        </button>
      </div>
    </div>
  </div>
</nav>