@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-6">📬 Contact Messages</h1>

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
            <td class="p-4">{{ $msg->category ?? '—' }}</td>
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
                <a href="{{ route('admin.emails.index', ['archived' => false]) }}" class="mr-4">Inbox</a>
                <a href="{{ route('admin.emails.index', ['archived' => true]) }}">Archive</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-6">
    {{ $messages->links() }}
</div>
@endsection