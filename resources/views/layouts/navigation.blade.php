<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

            <!-- Right: Main Nav Links -->
            <div class="hidden lg:flex items-center space-x-6 text-sm lg:text-base whitespace-nowrap overflow-x-auto no-scrollbar">
                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')" class="whitespace-nowrap">Home</x-nav-link>
                <x-nav-link href="{{ route('history.index') }}" :active="request()->routeIs('history.*')" class="whitespace-nowrap">History</x-nav-link>

                <div class="flex items-center space-x-1 whitespace-nowrap">
                    <x-nav-link href="{{ route('shop.index') }}" :active="request()->routeIs('shop.index', 'shop.show')" class="whitespace-nowrap">
                        {{ __('Shop') }}
                    </x-nav-link>

                    @if (request()->is('shop*') || request()->is('cart'))
                        <a href="{{ route('shop.cart.index') }}" class="relative text-gray-600 hover:text-gray-900 transition hover:scale-110 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.293 1.293a1 1 0 001.414 1.414L7 13zm10 0l1.293 1.293a1 1 0 01-1.414 1.414L17 13z" />
                            </svg>
                            @if (session('cart') && count(session('cart')) > 0)
                                <span id="cart-badge" class="absolute top-0 right-0 inline-flex items-center justify-center px-1 py-0.5 text-xs font-bold text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                                    {{ array_sum(array_column(session('cart'), 'quantity')) }}
                                </span>
                            @endif
                        </a>
                    @endif
                </div>

                <x-nav-link href="{{ route('blog.index') }}" :active="request()->routeIs('blog.index')" class="whitespace-nowrap">Blog</x-nav-link>
                <x-nav-link href="{{ route('calendar') }}" :active="request()->routeIs('calendar')" class="whitespace-nowrap">Calendar</x-nav-link>
                <x-nav-link href="{{ route('resources') }}" :active="request()->routeIs('resources')" class="whitespace-nowrap">Rally Resources</x-nav-link>
                <x-nav-link href="{{ route('charity.index') }}" :active="request()->routeIs('charity.index')" class="whitespace-nowrap">Charity Work</x-nav-link>

                @can('access-admin')
                    <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')" class="whitespace-nowrap">
                        Admin Panel
                    </x-nav-link>
                @endcan
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

                <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('profile.public', Auth::id()) }}">Profile</x-responsive-nav-link>
                <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('dashboard') }}">Dashboard</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            @else
                <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('login') }}">Log in</x-responsive-nav-link>
                <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('register') }}">Register</x-responsive-nav-link>
            @endauth

            <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('home') }}" :active="request()->routeIs('home')">Home</x-responsive-nav-link>
            <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('history.index') }}" :active="request()->routeIs('history.*')">History</x-responsive-nav-link>
            <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('shop.index') }}" :active="request()->routeIs('shop.index')">Shop</x-responsive-nav-link>
            <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('blog.index') }}" :active="request()->routeIs('blog.index')">Blog</x-responsive-nav-link>
            <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('calendar') }}" :active="request()->routeIs('calendar')">Calendar</x-responsive-nav-link>
            <x-responsive-nav-link class="whitespace-nowrap" href="{{ route('resources') }}" :active="request()->routeIs('resources')">Rally Resources</x-responsive-nav-link>
            <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('charity.index') }}" :active="request()->routeIs('charity.index')">Charity Work</x-responsive-nav-link>

            @can('access-admin')
                <x-responsive-nav-link class="text-sm lg:text-base whitespace-nowrap" href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')">
                    Admin Panel
                </x-responsive-nav-link>
            @endcan
        </div>
    </div>
</nav>