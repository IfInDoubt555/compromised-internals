<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Compromised Internals') }}</title>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    {{-- reCAPTCHA --}}
    <script
        src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"
        async
        defer>
    </script>
</head>

<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>

    @stack('scripts')
</body>

</html>