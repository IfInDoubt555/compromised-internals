<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Set dark mode ASAP to avoid FOUC --}}
    <script>
    (() => {
      const r = document.documentElement;
      const saved = localStorage.getItem('theme');
      const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches;
      if ((saved && saved === 'dark') || (!saved && prefersDark)) r.classList.add('dark');
    })();
    </script>

    <title>Admin | {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="font-sans antialiased
             text-gray-900 bg-gray-100
             dark:text-stone-100 dark:bg-stone-950">
    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        <aside class="w-64 hidden md:block
                       bg-gray-900 text-white
                       dark:bg-stone-900 dark:text-stone-100">
            <a href="{{ route('admin.dashboard') }}"
               class="block p-6 text-xl font-bold border-b border-gray-700 hover:text-blue-400 transition
                      dark:border-white/10 dark:hover:text-sky-300">
                Admin Panel
            </a>

            <nav class="p-4 space-y-2 text-sm">
                @can('access-admin')
                <a href="{{ route('admin.attributions.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-700
                          {{ request()->routeIs('admin.attributions.*') ? 'bg-gray-800 dark:bg-stone-800' : 'dark:hover:bg-stone-800' }}">
                    📸 Image Attributions
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-700
                          {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 dark:bg-stone-800' : 'dark:hover:bg-stone-800' }}">
                    👥 Manage Users
                </a>

                <a href="{{ route('admin.posts.moderation') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-700
                          {{ request()->routeIs('admin.posts.*') ? 'bg-gray-800 dark:bg-stone-800' : 'dark:hover:bg-stone-800' }}">
                    📝 Blog Moderation
                </a>

                <a href="{{ route('admin.events.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-700
                          {{ request()->routeIs('admin.events.*') ? 'bg-gray-800 dark:bg-stone-800' : 'dark:hover:bg-stone-800' }}">
                     Rally Events
                </a>

                <a href="{{ route('admin.emails.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-700
                          {{ request()->routeIs('admin.emails.*') ? 'bg-gray-800 dark:bg-stone-800' : 'dark:hover:bg-stone-800' }}">
                    ✉️ Email Inbox
                </a>

                <a href="{{ route('admin.travel-highlights.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-700
                          {{ request()->routeIs('admin.travel-highlights.*') ? 'bg-gray-800 dark:bg-stone-800' : 'dark:hover:bg-stone-800' }}">
                    🧭 Travel Highlights
                </a>
                @endcan
            </nav>
        </aside>

        {{-- Main --}}
        <main class="flex-1 p-6">
            <div class="mb-6">
                <a href="/" class="text-blue-600 hover:underline dark:text-sky-300 dark:hover:text-sky-200">← Back to Site</a>
            </div>

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>