@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">🛠️ Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Admin Info Card -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-2">Welcome, {{ Auth::user()->name }}</h2>
            <p class="text-gray-600 text-sm">Email: {{ Auth::user()->email }}</p>
            <p class="text-green-600 text-sm mt-1 font-semibold">Role: Admin</p>
        </div>

        <!-- Attribution Tool Card -->
        <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-semibold mb-2">📸 Attribution Tool</h2>
                <p class="text-gray-600 text-sm mb-4">Manage credit info for uploaded rally images.</p>
            </div>
            <a href="{{ route('admin.attributions.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                Open Attribution Manager
            </a>
        </div>

        <!-- Add More Admin Widgets Here -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-2">🚧 Coming Soon</h2>
            <p class="text-gray-600 text-sm">More tools and reports will appear here as the admin panel grows.</p>
        </div>
    </div>
</div>
@endsection
