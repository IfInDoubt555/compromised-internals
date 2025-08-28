<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-full">
        <div class="flex justify-between h-16 items-center">

            <!-- Left: Logo + Auth Controls -->
            <div class="flex items-center space-x-8 whitespace-nowrap">
                <a href="{{ route('home') }}" class="text-xl font-bold text-red-600 whitespace-nowrap">
                    Compromised Internals
                </a>

                @auth
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="hidden sm:inline-flex items-center px-3 py-2 text-sm lg:text-lg font-semibold text-gray-500 hover:text-gray-700 transition whitespace-nowrap">
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
                    <div class="hidden sm:flex items-center space-x-2 text-sm lg:text-base whitespace-nowrap">
                        <x-nav-link href="{{ route('login') }}">Log in</x-nav-link>
                        <x-nav-link href="{{ route('register') }}">Register</x-nav-link>
                    </div>
                @endauth
            </div>

            @php
                // Safe URL helpers: if a named route is missing (e.g., special 503 render), fall back to a plain URL
                $url = function ($name, $fallback) {
                    return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
                };
            @endphp

            <!-- Right: Main Nav Links -->
            <div class="hidden lg:flex items-center space-x-6 text-sm lg:text-base whitespace-nowrap overflow-x-auto no-scrollbar">
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

                @can('access-admin')
                    <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')" class="whitespace-nowrap">
                        Admin Panel
                    </x-nav-link>
                @endcan

                {{-- Theme Switch (pill) --}}
                <button
                  x-data
                  @click="$store.theme.toggle()"
                  :aria-pressed="$store.theme.dark"
                  role="switch"
                  aria-label="Toggle dark mode"
                  class="group relative inline-flex items-center justify-center select-none"
                >
                  <!-- Track -->
                  <span
                    class="relative w-16 h-9 rounded-full ring-1 transition-all duration-300
                           ring-stone-900/10 bg-stone-200
                           dark:ring-white/10 dark:bg-stone-700"
                  >
                    <!-- Static left hint: moon; hidden in LIGHT -->
                    <span class="absolute inset-y-0 left-1 flex items-center">
                      <span x-cloak class="opacity-0 dark:opacity-60 transition-opacity duration-300 text-sky-500"
                            aria-hidden="true">🌙</span>
                    </span>
                              
                    <!-- Static right hint: sun; hidden in DARK -->
                    <span class="absolute inset-y-0 right-1 flex items-center">
                      <span x-cloak class="opacity-60 dark:opacity-0 transition-opacity duration-300 text-amber-400"
                            aria-hidden="true">☀️</span>
                    </span>
                              
                    <!-- Knob (active icon only) -->
                    <span
                      class="absolute top-1 left-1 size-7 rounded-full shadow-sm ring-1 transition-all duration-300
                             ring-stone-900/10 bg-white
                             dark:ring-white/10 dark:bg-stone-800
                             flex items-center justify-center text-base"
                      :class="$store.theme.dark ? 'translate-x-0' : 'translate-x-7'"
                    >
                      <span x-cloak x-text="$store.theme.dark ? '🌙' : '☀️'"
                            :class="$store.theme.dark ? 'text-sky-300' : 'text-amber-400'"></span>
                      <span class="sr-only" x-text="$store.theme.dark ? 'Dark mode on' : 'Light mode on'"></span>
                    </span>
                  </span>
                </button>
            </div>

            <!-- Hamburger Menu (Mobile) -->
            <div class="flex lg:hidden items-center">
                <button @click="open = ! open" class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:hidden text-sm lg:text-base whitespace-nowrap">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <div class="px-4 border-t border-gray-200 pt-4 pb-1">
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

            {{-- Theme switch in mobile menu --}}
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
                  <!-- Static left hint: moon; hidden in LIGHT (to avoid duplicate with knob) -->
                  <span class="absolute inset-y-0 left-1 flex items-center">
                    <span
                      x-cloak
                      class="opacity-0 dark:opacity-60 transition-opacity duration-300 text-sky-500"
                      aria-hidden="true">🌙</span>
                  </span>

                  <!-- Static right hint: sun; hidden in DARK (to avoid duplicate with knob) -->
                  <span class="absolute inset-y-0 right-1 flex items-center">
                    <span
                      x-cloak
                      class="opacity-60 dark:opacity-0 transition-opacity duration-300 text-amber-400"
                      aria-hidden="true">☀️</span>
                  </span>

                  <!-- Knob shows the ACTIVE state icon -->
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