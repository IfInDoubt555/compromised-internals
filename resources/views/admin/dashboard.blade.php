@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col items-center justify-center mb-6 text-center">
        <h1 class="text-3xl font-bold mb-2">🛠️ Admin Dashboard</h1>
        <div class="bg-white rounded-xl shadow p-4 text-sm">
            <p class="font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}</p>
            <p class="text-gray-600">{{ Auth::user()->email }}</p>
            <p class="text-green-600 font-semibold">Role: Admin</p>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="text-sm text-gray-500">Registered Users</div>
            <div class="text-2xl font-semibold">128</div>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <div class="text-sm text-gray-500">Blog Posts</div>
            <div class="text-2xl font-semibold">64</div>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <div class="text-sm text-gray-500">Rally Events</div>
            <div class="text-2xl font-semibold">135</div>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <div class="text-sm text-gray-500">Images Uploaded</div>
            <div class="text-2xl font-semibold">89</div>
        </div>
    </div>

    <!-- Alerts & Notices -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 rounded mb-10">
        <p class="font-medium">Attention Needed:</p>
        <ul class="list-disc list-inside text-sm mt-2">
            <li>5 images missing attribution</li>
            <li>3 blog posts pending review</li>
            <li>2 rally events missing date/location</li>
        </ul>
    </div>

    <!-- Activity Feed -->
    <div class="bg-white rounded-xl shadow p-6 mb-10">
        <h2 class="text-xl font-semibold mb-4">📌 Recent Activity</h2>
        <ul class="text-sm text-gray-700 space-y-2">
            <li><span class="text-green-600">✅</span> User <strong>rallyfan34</strong> submitted a new blog post</li>
            <li><span class="text-blue-600">🖼️</span> New image uploaded: <strong>1974-acropolis.webp</strong></li>
            <li><span class="text-orange-500">✏️</span> Event <strong>2006 Rally Finland</strong> updated by Admin</li>
        </ul>
    </div>
</div>
@endsection