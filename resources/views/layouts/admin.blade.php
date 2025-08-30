<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

<body class="font-sans antialiased min-h-screen bg-gray-100 text-gray-900 dark:bg-stone-950 dark:text-stone-100">
  <div class="min-h-screen flex">

    {{-- Sidebar (mobile drawer + md+ static) --}}
    <aside id="admin-sidebar"
           class="fixed inset-y-0 left-0 z-40 h-full w-80 max-w-[86vw]
                  -translate-x-full md:translate-x-0
                  transition-transform duration-300 ease-out
                  bg-white/95 dark:bg-stone-900/95
                  shadow-2xl ring-1 ring-black/5 dark:ring-white/10
                  md:relative md:z-auto md:w-64 md:flex md:flex-col
                  md:shadow-none md:ring-0 md:bg-white dark:md:bg-stone-900
                  md:border-r md:border-gray-200 dark:md:border-white/10
                  rounded-r-2xl md:rounded-none overflow-y-auto backdrop-blur">

      {{-- Drawer header (mobile only) --}}
      <div class="flex md:hidden items-center justify-between px-4 h-14 border-b border-black/10 dark:border-white/10">
        <span class="font-semibold">Admin Panel</span>
        {{-- IMPORTANT: this is the CLOSE button, not open --}}
        <button id="admin-sidebar-close"
                class="inline-flex items-center justify-center size-10 rounded-full
                       shadow-lg ring-1 ring-black/10 dark:ring-white/10
                       bg-white/90 dark:bg-stone-800/90 backdrop-blur
                       text-stone-700 dark:text-stone-200 hover:bg-white dark:hover:bg-stone-800"
                aria-label="Close menu">✕</button>
      </div>

      {{-- Brand (md+) --}}
      <a href="{{ route('admin.dashboard') }}"
         class="hidden md:block p-6 text-xl font-bold border-b border-gray-200 transition
                hover:text-blue-600 dark:border-white/10 dark:hover:text-sky-300">
        Admin Panel
      </a>

      {{-- Nav --}}
      <nav class="p-4 space-y-1 text-sm">
        @can('access-admin')
          <a href="{{ route('admin.attributions.index') }}"
             class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 dark:hover:bg-stone-800/80 {{ request()->routeIs('admin.attributions.*') ? 'bg-gray-100/80 dark:bg-stone-800/80' : '' }}">
            <span class="text-lg">📸</span><span class="font-medium">Image Attributions</span>
          </a>
          <a href="{{ route('admin.users.index') }}"
             class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 dark:hover:bg-stone-800/80 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100/80 dark:bg-stone-800/80' : '' }}">
            <span class="text-lg">👥</span><span class="font-medium">Manage Users</span>
          </a>
          <a href="{{ route('admin.posts.moderation') }}"
             class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 dark:hover:bg-stone-800/80 {{ request()->routeIs('admin.posts.*') ? 'bg-gray-100/80 dark:bg-stone-800/80' : '' }}">
            <span class="text-lg">📝</span><span class="font-medium">Blog Moderation</span>
          </a>
          <a href="{{ route('admin.events.index') }}"
             class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 dark:hover:bg-stone-800/80 {{ request()->routeIs('admin.events.*') ? 'bg-gray-100/80 dark:bg-stone-800/80' : '' }}">
            <span class="text-lg">🗓️</span><span class="font-medium">Rally Events</span>
          </a>
          <a href="{{ route('admin.emails.index') }}"
             class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 dark:hover:bg-stone-800/80 {{ request()->routeIs('admin.emails.*') ? 'bg-gray-100/80 dark:bg-stone-800/80' : '' }}">
            <span class="text-lg">✉️</span><span class="font-medium">Email Inbox</span>
          </a>
          <a href="{{ route('admin.travel-highlights.index') }}"
             class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 dark:hover:bg-stone-800/80 {{ request()->routeIs('admin.travel-highlights.*') ? 'bg-gray-100/80 dark:bg-stone-800/80' : '' }}">
            <span class="text-lg">🧭</span><span class="font-medium">Travel Highlights</span>
          </a>
          <a href="{{ route('admin.affiliates.clicks') }}"
             class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 dark:hover:bg-stone-800/80 {{ request()->routeIs('admin.affiliates.*') ? 'bg-gray-100/80 dark:bg-stone-800/80' : '' }}">
            <span class="text-lg">📈</span><span class="font-medium">Affiliate Clicks</span>
          </a>
        @endcan
      </nav>
    </aside>

    {{-- Backdrop --}}
    <div id="admin-sidebar-overlay"
         class="fixed inset-0 z-30 hidden md:hidden opacity-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300"></div>

    {{-- MAIN (was missing) --}}
    <main class="flex-1 p-6">
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
          {{-- Mobile: open sidebar --}}
          <button id="admin-sidebar-open"
                  class="md:hidden inline-flex items-center justify-center size-10 rounded-full
                         shadow-lg ring-1 ring-black/10 dark:ring-white/10
                         bg-white/90 dark:bg-stone-800/90 backdrop-blur
                         text-stone-700 dark:text-stone-200 hover:bg-white dark:hover:bg-stone-800"
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
    // Theme toggle
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
  (() => {
    const openBtn = document.getElementById('admin-sidebar-open');
    const closeBtn = document.getElementById('admin-sidebar-close');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');
    const main = document.querySelector('main');

    let lastFocused = null;

    const open = () => {
      lastFocused = document.activeElement;
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      requestAnimationFrame(() => overlay.classList.add('opacity-100'));
      openBtn?.setAttribute('aria-expanded','true');
      document.body.classList.add('overflow-hidden');
      main?.setAttribute('aria-hidden','true');
      sidebar.querySelector('a[href]')?.focus({preventScroll:true});
    };

    const close = () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.remove('opacity-100');
      setTimeout(() => overlay.classList.add('hidden'), 200);
      openBtn?.setAttribute('aria-expanded','false');
      document.body.classList.remove('overflow-hidden');
      main?.removeAttribute('aria-hidden');
      lastFocused?.focus?.();
    };

    openBtn?.addEventListener('click', open);
    closeBtn?.addEventListener('click', close);
    overlay?.addEventListener('click', close);
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

    const mq = window.matchMedia('(min-width: 768px)');
    mq.addEventListener?.('change', e => {
      if (e.matches) {
        overlay.classList.add('hidden'); overlay.classList.remove('opacity-100');
        sidebar.classList.remove('-translate-x-full');
        document.body.classList.remove('overflow-hidden');
        openBtn?.setAttribute('aria-expanded','false');
        main?.removeAttribute('aria-hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
      }
    });
  })();
  </script>

  @stack('scripts')
</body>
</html>