@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex flex-col items-center justify-center mb-6 text-center">
    <h1 class="text-3xl font-bold mb-2">🛠️ Admin Dashboard</h1>

    <div class="ci-admin-card text-sm">
      <p class="font-semibold text-gray-900 dark:text-gray-100">
        Welcome, {{ Auth::user()->name }}
      </p>
      <p class="ci-muted">{{ Auth::user()->email }}</p>
      <p class="text-green-600 dark:text-green-400 font-semibold">Role: Admin</p>
    </div>
  </div>

  {{-- Stats Overview --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="ci-admin-card p-4">
      <div class="text-sm ci-muted">Registered Users</div>
      <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $userCount }}</div>
    </div>

    <div class="ci-admin-card p-4">
      <div class="text-sm ci-muted">Blog Posts</div>
      <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $postCount }}</div>
    </div>

    <div class="ci-admin-card p-4">
      <div class="text-sm ci-muted">Rally Events</div>
      <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">135</div>
    </div>

    <div class="ci-admin-card p-4">
      <div class="text-sm ci-muted">Images Uploaded</div>
      <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $imageCount }}</div>
    </div>
  </div>

  {{-- Alerts & Notices --}}
  <div class="ci-alert ci-alert-warn mb-10">
    <p class="font-medium">Attention Needed:</p>
    <ul class="list-disc list-inside text-sm mt-2">
      <li>5 images missing attribution</li>
      <li>3 blog posts pending review</li>
      <li>2 rally events missing date/location</li>
    </ul>
  </div>

  {{-- Activity Feed --}}
  <div class="ci-admin-card mb-10">
    <h2 class="text-xl font-semibold mb-4">📌 Recent Activity</h2>
    <ul class="text-sm space-y-2">
      <li>
        <span class="text-green-600 dark:text-green-400">✅</span>
        User <strong>{{ 'rallyfan34' }}</strong> submitted a new blog post
      </li>
      <li>
        <span class="text-blue-600 dark:text-blue-400">🖼️</span>
        New image uploaded: <strong>1974-acropolis.webp</strong>
      </li>
      <li>
        <span class="text-orange-500 dark:text-orange-400">✏️</span>
        Event <strong>2006 Rally Finland</strong> updated by Admin
      </li>
    </ul>
  </div>
</div>
@endsection