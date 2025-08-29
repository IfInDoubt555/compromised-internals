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

<body class="font-sans antialiased min-h-screen
             bg-gray-100 text-gray-900
             dark:bg-stone-950 dark:text-stone-100">
  <div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 hidden md:flex md:flex-col
                   bg-white text-gray-900 border-r border-gray-200
                   dark:bg-stone-900 dark:text-stone-100 dark:border-white/10">
      <a href="{{ route('admin.dashboard') }}"
         class="block p-6 text-xl font-bold border-b border-gray-200 transition
                hover:text-blue-600 dark:border-white/10 dark:hover:text-sky-300">
        Admin Panel
      </a>

      <nav class="p-4 space-y-2 text-sm">
        @can('access-admin')
          <a href="{{ route('admin.attributions.index') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.attributions.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
           📸 Image Attributions
          </a>

          <a href="{{ route('admin.users.index') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
          👥 Manage Users
          </a>

          <a href="{{ route('admin.posts.moderation') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.posts.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
           📝 Blog Moderation
          </a>

          <a href="{{ route('admin.events.index') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.events.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
           🗓️ Rally Events
          </a>

          <a href="{{ route('admin.emails.index') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.emails.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
           ✉️ Email Inbox
          </a>

          <a href="{{ route('admin.travel-highlights.index') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.travel-highlights.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
           🧭 Travel Highlights
          </a>

          {{-- NEW: Affiliate Clicks report --}}
          <a href="{{ route('admin.affiliates.clicks') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.affiliates.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
           📈 Affiliate Clicks
          </a>
        @endcan
      </nav>
    </aside>

    {{-- Main --}}
    <main class="flex-1 p-6">
      <div class="mb-6 flex items-center justify-between">
        <a href="/" class="ci-link">← Back to Site</a>

        {{-- Theme toggle --}}
        <button id="theme-toggle" type="button" aria-label="Toggle theme"
                class="ci-btn-ghost text-sm">
          <span id="theme-toggle-icon">🌙</span>
          <span class="hidden sm:inline" id="theme-toggle-text">Dark</span>
        </button>
      </div>

      @yield('content')
    </main>
  </div>

  <script>
    // Simple theme toggle that mirrors the FOUC-prevent script
    (function () {
      const btn = document.getElementById('theme-toggle');
      const icon = document.getElementById('theme-toggle-icon');
      const label = document.getElementById('theme-toggle-text');

      function setLabel() {
        const isDark = document.documentElement.classList.contains('dark');
        icon.textContent = isDark ? '☀️' : '🌙';
        if (label) label.textContent = isDark ? 'Light' : 'Dark';
      }

      setLabel();

      btn?.addEventListener('click', () => {
        const r = document.documentElement;
        const isDark = r.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        setLabel();
      });
    })();
  </script>

  @stack('scripts')
</body>
</html>