<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white hidden md:block">
            <div class="p-6 text-xl font-bold border-b border-gray-700">
                Admin Panel
            </div>
            <nav class="p-4 space-y-2">
                @can('access-admin')
                    <a href="{{ route('admin.attributions.index') }}"
                       class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('attributions.*') ? 'bg-gray-800' : '' }}">
                        📸 Image Attributions
                    </a>
                @endcan
                <!-- You can add more admin-only links below -->
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="mb-6">
                <a href="/" class="text-blue-600 hover:underline">← Back to Site</a>
            </div>

            @yield('content')
        </main>
    </div>
</body>
</html>
