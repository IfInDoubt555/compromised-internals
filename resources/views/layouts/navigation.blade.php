<nav x-data="{ open: false }"
     class="sticky top-0 z-50 bg-white/80 border-b border-black/5
            dark:bg-stone-950/70 dark:border-white/10
            supports-[backdrop-filter]:backdrop-blur">
  <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-full">

    {{-- ===== Row 1: Logo | Admin | Auth + Theme + Hamburger ===== --}}
    <div class="flex items-center justify-between h-16 gap-2">

      {{-- Left: logo --}}
      <div class="min-w-0 flex items-center space-x-2">
        <a href="{{ route('home') }}"
           class="text-xl font-bold text-red-600 dark:text-red-400 truncate">
          Compromised Internals
        </a>
      </div>

      {{-- Center: Admin (desktop only) --}}
      <div class="hidden lg:block">
        @can('access-admin')
          <a href="{{ route('admin.dashboard') }}"
             class="inline-block rounded px-2 py-1 text-sm font-semibold text-blue-700 dark:text-blue-400 hover:underline">
            Admin Panel
          </a>
        @endcan
      </div>

      {{-- Right: auth, theme (desktop), hamburger --}}
      <div class="flex items-center gap-3">

        @auth
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="hidden sm:inline-flex items-center px-3 py-2 text-sm lg:text-lg font-semibold
                             text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white transition">
                {{ Auth::user()->name }}
                <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06 0L10 10.91l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 010-1.06z" clip-rule="evenodd"/>
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
          <div class="hidden sm:flex items-center space-x-2 text-sm lg:text-base">
            <x-nav-link href="{{ route('login') }}">Log in</x-nav-link>
            <x-nav-link href="{{ route('register') }}">Register</x-nav-link>
          </div>
        @endauth

        {{-- Theme control (desktop) --}}
        <div class="hidden lg:flex items-center" x-data>
          <div role="tablist" aria-label="Color theme"
               class="inline-flex rounded-xl ring-1 ring-stone-900/10 dark:ring-white/10
                      bg-stone-200 dark:bg-stone-700 p-1 gap-1">

            <button type="button" role="tab" aria-label="Light"
                  @click="$store.theme.setMode('light')"
                  :aria-selected="($store.theme.mode==='light').toString()"
                  class="px-3 py-2 rounded-lg text-base inline-flex items-center gap-2 transition-all"
                  :class="$store.theme.mode==='light'
          ? 'bg-white text-stone-900 shadow-md ring-1 ring-amber-400/35 shadow-amber-200/25 dark:bg-stone-100'
          : 'text-stone-700 hover:text-stone-900 dark:text-stone-300 dark:hover:text-white'">
              <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.8 1.42-1.42zM1 13h3v-2H1v2zm10-9h2V1h-2v3zm7.04 1.46l1.79-1.8-1.41-1.41-1.8 1.79 1.42 1.42zM17 13h3v-2h-3v2zm-5 8h2v-3h-2v3zm6.24-1.84l1.8 1.79 1.41-1.41-1.79-1.8-1.42 1.42zM4.96 18.54l-1.79 1.8 1.41 1.41 1.8-1.79-1.42-1.42zM12 6a6 6 0 100 12 6 6 0 000-12z"/></svg>
          </button>

          <button type="button" role="tab" aria-label="System"
                  @click="$store.theme.setMode('system')"
                  :aria-selected="($store.theme.mode==='system').toString()"
                  class="px-3 py-2 rounded-lg text-base inline-flex items-center gap-2 transition-all"
                  :class="$store.theme.mode==='system'
          ? 'bg-white text-stone-900 shadow-md ring-1 ring-indigo-400/35 shadow-indigo-200/25 dark:bg-stone-100'
          : 'text-stone-700 hover:text-stone-900 dark:text-stone-300 dark:hover:text-white'">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M4 5h16a2 2 0 012 2v8a2 2 0 01-2 2h-6v2h3v2H7v-2h3v-2H4a2 2 0 01-2-2V7a2 2 0 012-2zm0 2v8h16V7H4z"/></svg>
          </button>

          <button type="button" role="tab" aria-label="Dark"
                  @click="$store.theme.setMode('dark')"
                  :aria-selected="($store.theme.mode==='dark').toString()"
                  class="px-3 py-2 rounded-lg text-base inline-flex items-center gap-2 transition-all"
                  :class="$store.theme.mode==='dark'
          ? 'bg-white text-stone-900 shadow-md ring-1 ring-sky-400/35 shadow-sky-200/25 dark:bg-stone-100'
          : 'text-stone-700 hover:text-stone-900 dark:text-stone-300 dark:hover:text-white'">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/></svg>
          </button>
          </div>
        </div>

        {{-- Hamburger (mobile) --}}
        <button @click="open = !open"
                class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100
                       dark:text-gray-300 dark:hover:text-white dark:hover:bg-zinc-800 rounded-md">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
          <span class="sr-only">Toggle menu</span>
        </button>

      </div> {{-- /right --}}
    </div> {{-- /row 1 --}}

    {{-- helper for fallbacks --}}
    @php
      $url = fn ($name, $fallback) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
    @endphp

    {{-- ===== Row 2: Primary nav (desktop) ===== --}}
    <div class="hidden lg:block text-gray-800 dark:text-gray-200">
      <div class="py-2">
        <div class="flex items-center justify-center gap-x-6 gap-y-2 text-sm lg:text-base whitespace-nowrap overflow-x-auto no-scrollbar">
          <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">Home</x-nav-link>
          <x-nav-link href="{{ $url('calendar.index','/calendar') }}" :active="request()->routeIs('calendar.index')">Rally Schedule</x-nav-link>
          <x-nav-link href="{{ $url('travel.plan','/travel/plan') }}" :active="request()->routeIs('travel.plan')">Plan Your Trip</x-nav-link>
          <x-nav-link href="{{ $url('history.index','/history') }}" :active="request()->routeIs('history.*')">History</x-nav-link>
          <x-nav-link href="{{ $url('blog.index','/blog') }}" :active="request()->routeIs('blog.index')">Blog</x-nav-link>
          <x-nav-link href="{{ $url('resources','/rally-resources') }}" :active="request()->routeIs('resources')">Rally Resources</x-nav-link>
          <x-nav-link href="{{ $url('shop.index','/shop') }}" :active="request()->routeIs('shop.index') || request()->routeIs('shop.show')">Shop</x-nav-link>
          <x-nav-link href="{{ $url('charity.index','/charity') }}" :active="request()->routeIs('charity.index')">Charity Work</x-nav-link>
        </div>
      </div>
    </div>

  </div> {{-- /container --}}

  {{-- ===== Mobile dropdown ===== --}}
  <div x-cloak x-show="open" x-transition
       class="lg:hidden text-sm lg:text-base whitespace-nowrap bg-white/95 dark:bg-zinc-900/95
              text-gray-800 dark:text-gray-200 border-t border-black/5 dark:border-white/10">
    <div class="pt-2 pb-3 space-y-1">
      @auth
        <div class="px-4 border-t border-gray-200 dark:border-zinc-800 pt-4 pb-1">
          <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
          <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
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
      <x-responsive-nav-link href="{{ $url('calendar.index','/calendar') }}" :active="request()->routeIs('calendar.index')">Rally Schedule</x-responsive-nav-link>
      <x-responsive-nav-link href="{{ $url('travel.plan','/travel/plan') }}" :active="request()->routeIs('travel.plan')">Plan Your Trip</x-responsive-nav-link>
      <x-responsive-nav-link href="{{ $url('history.index','/history') }}" :active="request()->routeIs('history.*')">History</x-responsive-nav-link>
      <x-responsive-nav-link href="{{ $url('blog.index','/blog') }}" :active="request()->routeIs('blog.index')">Blog</x-responsive-nav-link>
      <x-responsive-nav-link href="{{ $url('resources','/rally-resources') }}" :active="request()->routeIs('resources')">Rally Resources</x-responsive-nav-link>
      <x-responsive-nav-link href="{{ $url('shop.index','/shop') }}" :active="request()->routeIs('shop.index') || request()->routeIs('shop.show')">Shop</x-responsive-nav-link>
      <x-responsive-nav-link href="{{ $url('charity.index','/charity') }}" :active="request()->routeIs('charity.index')">Charity Work</x-responsive-nav-link>

      {{-- Mobile theme control (same icons/colors as desktop) --}}
      <div class="px-4 py-2" x-data>
        <div role="tablist" aria-label="Color theme"
             class="inline-flex rounded-xl ring-1 ring-stone-900/10 dark:ring-white/10 bg-stone-200 dark:bg-stone-700 p-1 gap-1">
          <button type="button" role="tab" aria-label="Light"
                  @click="$store.theme.setMode('light')"
                  :aria-selected="($store.theme.mode==='light').toString()"
                  class="px-3 py-2 rounded-lg text-base inline-flex items-center gap-2 transition-all"
                  :class="$store.theme.mode==='light'
          ? 'bg-white text-stone-900 shadow-md ring-1 ring-amber-400/35 shadow-amber-200/25 dark:bg-stone-100'
          : 'text-stone-700 hover:text-stone-900 dark:text-stone-300 dark:hover:text-white'">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.8 1.42-1.42zM1 13h3v-2H1v2zm10-9h2V1h-2v3zm7.04 1.46l1.79-1.8-1.41-1.41-1.8 1.79 1.42 1.42zM17 13h3v-2h-3v2zm-5 8h2v-3h-2v3zm6.24-1.84l1.8 1.79 1.41-1.41-1.79-1.8-1.42 1.42zM4.96 18.54l-1.79 1.8 1.41 1.41 1.8-1.79-1.42-1.42zM12 6a6 6 0 100 12 6 6 0 000-12z"/></svg>
          </button>

          <button type="button" role="tab" aria-label="System"
                  @click="$store.theme.setMode('system')"
                  :aria-selected="($store.theme.mode==='system').toString()"
                  class="px-3 py-2 rounded-lg text-base inline-flex items-center gap-2 transition-all"
                  :class="$store.theme.mode==='system'
          ? 'bg-white text-stone-900 shadow-md ring-1 ring-indigo-400/35 shadow-indigo-200/25 dark:bg-stone-100'
          : 'text-stone-700 hover:text-stone-900 dark:text-stone-300 dark:hover:text-white'">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M4 5h16a2 2 0 012 2v8a2 2 0 01-2 2h-6v2h3v2H7v-2h3v-2H4a2 2 0 01-2-2V7a2 2 0 012-2zm0 2v8h16V7H4z"/></svg>
          </button>

          <button type="button" role="tab" aria-label="Dark"
                  @click="$store.theme.setMode('dark')"
                  :aria-selected="($store.theme.mode==='dark').toString()"
                  class="px-3 py-2 rounded-lg text-base inline-flex items-center gap-2 transition-all"
                  :class="$store.theme.mode==='dark'
          ? 'bg-white text-stone-900 shadow-md ring-1 ring-sky-400/35 shadow-sky-200/25 dark:bg-stone-100'
          : 'text-stone-700 hover:text-stone-900 dark:text-stone-300 dark:hover:text-white'">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</nav>