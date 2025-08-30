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
    <aside id="admin-sidebar"
           class="fixed inset-y-0 left-0 z-40 w-72 transform -translate-x-full transition-transform duration-200 ease-out
                  bg-white text-gray-900 border-r border-gray-200
                  dark:bg-stone-900 dark:text-stone-100 dark:border-white/10
                  md:relative md:translate-x-0 md:w-64 md:flex md:flex-col">

      {{-- Mobile header inside drawer --}}
      <div class="flex md:hidden items-center justify-between px-4 h-14 border-b border-gray-200 dark:border-white/10">
        <span class="font-semibold">Admin Panel</span>
        <button id="admin-sidebar-close" class="ci-btn-ghost text-sm" aria-label="Close menu">✕</button>
      </div>

      <a href="{{ route('admin.dashboard') }}"
         class="hidden md:block p-6 text-xl font-bold border-b border-gray-200 transition
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
          <a href="{{ route('admin.affiliates.clicks') }}"
             class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-stone-800
                    {{ request()->routeIs('admin.affiliates.*') ? 'bg-gray-100 dark:bg-stone-800' : '' }}">
            📈 Affiliate Clicks
          </a>
        @endcan
      </nav>
    </aside>

    {{-- Backdrop for mobile drawer --}}
    <div id="admin-sidebar-overlay"
     class="fixed inset-0 z-30 bg-black/40 hidden md:hidden"></div>

    {{-- Main --}}
    <div class="mb-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        {{-- Mobile: open sidebar --}}
        <button id="admin-sidebar-open"
                class="md:hidden ci-btn-ghost text-sm"
                aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open menu">☰</button>

        <a href="/" class="ci-link">← Back to Site</a>
      </div>

      {{-- Theme toggle --}}
      <button id="theme-toggle" type="button" aria-label="Toggle theme" class="ci-btn-ghost text-sm">
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

  <script>
    (function () {
      const openBtn = document.getElementById('admin-sidebar-open');
      const closeBtn = document.getElementById('admin-sidebar-close');
      const sidebar  = document.getElementById('admin-sidebar');
      const overlay  = document.getElementById('admin-sidebar-overlay');
    
      const open = () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        openBtn?.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');
      };
      const close = () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        openBtn?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');
      };
    
      openBtn?.addEventListener('click', open);
      closeBtn?.addEventListener('click', close);
      overlay?.addEventListener('click', close);
      window.addEventListener('resize', () => {
        // Ensure correct state when growing past md
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('-translate-x-full');
          overlay.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        }
      });
    })();
  </script>

  @stack('scripts')
</body>
</html>