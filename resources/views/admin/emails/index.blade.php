@extends('layouts.admin')

@php
use App\Enums\ContactCategory;

$colorMap = [
  'General'          => 'bg-gray-200 text-gray-800 dark:bg-zinc-700 dark:text-gray-100',
  'Support'          => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200',
  'Feedback'         => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200',
  'Security'         => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200',
  'Media/Press'      => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200',
  'Business Inquiry' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200',
  'Shop & Orders'    => 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-200',
  'Legal'            => 'bg-black text-white dark:bg-zinc-800 dark:text-gray-100',
  'Feature Request'  => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200',
];
$query = request()->except('archived', 'page');
@endphp

@section('content')
<h1 class="text-2xl font-bold mb-6">📬 Contact Messages</h1>

<div class="mb-4">
  <a href="{{ route('admin.emails.index', array_merge($query, ['archived' => 0])) }}"
     class="mr-4 {{ request('archived') ? 'ci-link-muted' : 'font-bold' }}">📥 Inbox</a>
  <a href="{{ route('admin.emails.index', array_merge($query, ['archived' => 1])) }}"
     class="{{ request('archived') ? 'font-bold' : 'ci-link-muted' }}">🗃️ Archive</a>
</div>

<form method="GET" class="mb-6 flex flex-wrap items-center gap-4">
  <select name="category" class="ci-select text-sm">
    <option value="">All Categories</option>
    @foreach (ContactCategory::cases() as $category)
      <option value="{{ $category->value }}" @selected(request('category')==$category->value)>
        {{ $category->value }}
      </option>
    @endforeach
  </select>

  <select name="status" class="ci-select text-sm">
    <option value="">All Statuses</option>
    <option value="open" @selected(request('status')=='open')>Open</option>
    <option value="resolved" @selected(request('status')=='resolved')>Resolved</option>
  </select>

  <select name="sort" class="ci-select text-sm">
    <option value="">Sort by</option>
    <option value="name" @selected(request('sort')=='name')>Name</option>
    <option value="email" @selected(request('sort')=='email')>Email</option>
    <option value="created_at" @selected(request('sort')=='created_at')>Date</option>
  </select>

  <select name="direction" class="ci-select text-sm">
    <option value="asc" @selected(request('direction')=='asc')>Asc</option>
    <option value="desc" @selected(request('direction')=='desc')>Desc</option>
  </select>

  <button type="submit" class="ci-btn-primary text-sm">Filter</button>
  <a href="{{ route('admin.emails.index') }}" class="ci-link-muted text-sm">Reset</a>
</form>

<div class="overflow-x-auto ci-admin-card p-0">
  <table class="w-full table-auto text-sm">
    <thead class="bg-gray-100 dark:bg-zinc-800/70 text-gray-700 dark:text-gray-200 uppercase">
      <tr>
        <th class="p-4 text-left">Name</th>
        <th class="p-4 text-left">Email</th>
        <th class="p-4 text-left">Category</th>
        <th class="p-4 text-left">Status</th>
        <th class="p-4 text-left">Date</th>
        <th class="p-4 text-left">Action</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
      @forelse ($messages as $msg)
        <tr>
          <td class="p-4 font-medium text-gray-900 dark:text-gray-100">{{ $msg->name }}</td>
          <td class="p-4">
            <a href="mailto:{{ $msg->email }}" class="ci-link">{{ $msg->email }}</a>
          </td>
          <td class="p-4">
            @if ($msg->category)
              <span class="px-2 py-1 text-xs font-semibold rounded {{ $colorMap[$msg->category] ?? 'bg-gray-100 text-gray-700 dark:bg-zinc-800 dark:text-gray-200' }}">
                {{ ContactCategory::tryFrom($msg->category)?->value ?? $msg->category }}
              </span>
            @else
              —
            @endif
          </td>
          <td class="p-4">
            @if ($msg->resolved)
              <span class="text-green-600 dark:text-green-400 font-semibold">✔ Resolved</span>
            @else
              <span class="text-red-600 dark:text-red-400 font-semibold">⏳ Open</span>
            @endif
          </td>
          <td class="p-4 text-gray-600 dark:text-gray-300">{{ $msg->created_at->format('M d, Y') }}</td>
          <td class="p-4">
            <a href="{{ route('admin.emails.show', $msg->id) }}" class="ci-link">View</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="p-6 text-center ci-muted">No messages found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-6">
  {{ $messages->links() }}
</div>
@endsection