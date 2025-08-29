<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Ensure dark class is set ASAP to avoid FOUC --}}
    <script>
    (() => {
      const root = document.documentElement;
      const saved = localStorage.getItem('theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      if ((saved && saved === 'dark') || (!saved && prefersDark)) {
        root.classList.add('dark');
      } else {
        root.classList.remove('dark');
      }
    })();
    </script>

    <title>{{ config('app.name', 'Compromised Internals') }}</title>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Any page-specific head pushes --}}
    @stack('head')

    {{-- Osano CMP (Cookie Consent Manager) --}}
    <script id="osano-cmp"
            src="https://cmp.osano.com/68c885bf-d384-489c-a092-2092f351097c/osano.js"
            async></script>

    {{-- reCAPTCHA --}}
    <script
        src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"
        async defer>
    </script>
</head>

<body class="antialiased text-gray-900 bg-gray-100
             dark:text-stone-100 dark:bg-stone-950">
    <div class="min-h-screen w-full flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>

    {{-- Any page-specific scripts (your inline grecaptcha handlers, etc.) --}}
    @stack('scripts')
</body>
</html>