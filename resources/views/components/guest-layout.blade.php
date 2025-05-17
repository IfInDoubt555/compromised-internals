<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Compromised Internals') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ✅ Add this to inject reCAPTCHA script --}}
    @stack('head')
</head>

<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>

    {{-- ✅ Add this to inject inline form-handling script --}}
    @stack('scripts')
</body>

</html>