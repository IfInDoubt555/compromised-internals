@extends('layouts.admin')

@php
use App\Enums\ContactCategory;

$colorMap = [
'General' => 'bg-gray-200 text-gray-800',
'Support' => 'bg-blue-100 text-blue-700',
'Feedback' => 'bg-yellow-100 text-yellow-700',
'Security' => 'bg-red-100 text-red-700',
'Media/Press' => 'bg-purple-100 text-purple-700',
'Business Inquiry' => 'bg-green-100 text-green-700',
'Shop & Orders' => 'bg-pink-100 text-pink-700',
'Legal' => 'bg-black text-white',
'Feature Request' => 'bg-orange-100 text-orange-700',
];
@endphp

@section('content')
<h1 class="text-2xl font-bold mb-6">📬 Contact Messages</h1>

@php
$query = request()->except('archived', 'page');
@endphp

<div class="mb-4">
    <a href="{{ route('admin.emails.index', array_merge($query, ['archived' => 0])) }}"
        class="mr-4 {{ request('archived') ? 'text-gray-500' : 'font-bold' }}">📥 Inbox</a>
    <a href="{{ route('admin.emails.index', array_merge($query, ['archived' => 1])) }}"
        class="{{ request('archived') ? 'font-bold' : 'text-gray-500' }}">🗃️ Archive</a>
</div>


<form method="GET" class="mb-6 flex flex-wrap gap-4">
    <select name="category" class="border px-2 py-1 rounded">
        <option value="">All Categories</option>
        @foreach (\App\Enums\ContactCategory::cases() as $category)
        <option value="{{ $category->value }}" @selected(request('category')==$category->value)>
            {{ $category->value }}
        </option>
        @endforeach
    </select>

    <select name="status" class="border px-2 py-1 rounded">
        <option value="">All Statuses</option>
        <option value="open" @selected(request('status')=='open' )>Open</option>
        <option value="resolved" @selected(request('status')=='resolved' )>Resolved</option>
    </select>

    <select name="sort" class="border px-2 py-1 rounded">
        <option value="">Sort by</option>
        <option value="name" @selected(request('sort')=='name' )>Name</option>
        <option value="email" @selected(request('sort')=='email' )>Email</option>
        <option value="created_at" @selected(request('sort')=='created_at' )>Date</option>
    </select>

    <select name="direction" class="border px-2 py-1 rounded">
        <option value="asc" @selected(request('direction')=='asc' )>Asc</option>
        <option value="desc" @selected(request('direction')=='desc' )>Desc</option>
    </select>

    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Filter</button>
    <a href="{{ route('admin.emails.index') }}" class="underline text-sm text-gray-600">Reset</a>
</form>


<table class="w-full table-auto bg-white shadow rounded-lg overflow-hidden">
    <thead class="bg-gray-100 text-left text-sm uppercase text-gray-700">
        <tr>
            <th class="p-4">Name</th>
            <th class="p-4">Email</th>
            <th class="p-4">Category</th>
            <th class="p-4">Status</th>
            <th class="p-4">Date</th>
            <th class="p-4">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($messages as $msg)
        <tr class="border-t text-sm">
            <td class="p-4 font-medium">{{ $msg->name }}</td>
            <td class="p-4 text-blue-600">{{ $msg->email }}</td>
            <td class="p-4">
                @if ($msg->category)
                <span class="px-2 py-1 text-xs font-semibold rounded {{ $colorMap[$msg->category] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ ContactCategory::tryFrom($msg->category)?->value ?? $msg->category }}
                </span>
                @else
                —
                @endif
            </td>
            <td class="p-4">
                @if ($msg->resolved)
                <span class="text-green-600 font-semibold">✔ Resolved</span>
                @else
                <span class="text-red-600 font-semibold">⏳ Open</span>
                @endif
            </td>
            <td class="p-4">{{ $msg->created_at->format('M d, Y') }}</td>
            <td class="p-4">
                <a href="{{ route('admin.emails.show', $msg->id) }}" class="text-blue-500 hover:underline">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-6">
    {{ $messages->links() }}
</div>
@endsection