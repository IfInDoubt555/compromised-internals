@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">🛠️ Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
            <a href="{{ route('admin.attributions.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded text-center">
                Open Attribution Manager
            </a>
        </div>

        <!-- User Manager -->
        <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-semibold mb-2">👥 User Manager</h2>
                <p class="text-gray-600 text-sm mb-4">View, edit, or ban registered users.</p>
            </div>
            <a href="#" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded text-center">
                Manage Users
            </a>
        </div>

        <!-- Blog Moderation -->
        <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-semibold mb-2">📝 Blog Moderation</h2>
                <p class="text-gray-600 text-sm mb-4">Review or edit blog posts before and after publishing.</p>
            </div>
            <a href="#" class="inline-block bg-purple-600 hover:bg-purple-700 text-white text-sm px-4 py-2 rounded text-center">
                Moderate Posts
            </a>
        </div>

        <!-- Event Manager -->
        <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-semibold mb-2">🏁 Rally Event Manager</h2>
                <p class="text-gray-600 text-sm mb-4">Maintain and edit historical rally events and entries.</p>
            </div>
            <a href="#" class="inline-block bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded text-center">
                Manage Events
            </a>
        </div>

        <!-- Coming Soon Placeholder -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-2">🚧 More Coming Soon</h2>
            <p class="text-gray-600 text-sm">Additional tools and analytics modules will be added here.</p>
        </div>
    </div>
</div>
@endsection