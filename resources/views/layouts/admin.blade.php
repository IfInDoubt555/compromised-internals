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
            <a href="{{ route('dashboard') }}" class="block p-6 text-xl font-bold border-b border-gray-700 hover:text-blue-400 transition">
                Admin Panel
            </a>
            <nav class="p-4 space-y-2 text-sm">
                @can('access-admin')
                    <a href="{{ route('admin.attributions.index') }}"
                       class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.attributions.*') ? 'bg-gray-800' : '' }}">
                        📸 Image Attributions
                    </a>

                    <a href="#"
                       class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">
                        👥 Manage Users
                    </a>

                    <a href="{{ route('admin.posts.moderation') }}"
                       class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.posts.*') ? 'bg-gray-800' : '' }}">
                       📝 Blog Moderation
                    </a>

                    <a href="#"
                       class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.events.*') ? 'bg-gray-800' : '' }}">
                        🏁 Rally Events
                    </a>

                    <a href="#"
                       class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.analytics.*') ? 'bg-gray-800' : '' }}">
                        📊 Analytics (Coming Soon)
                    </a>
                @endcan
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