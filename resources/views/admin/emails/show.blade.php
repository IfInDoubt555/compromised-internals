@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">📨 Message from {{ $message->name }}</h1>

<div class="bg-white p-6 shadow rounded-xl space-y-4">
    <p><strong>Email:</strong> {{ $message->email }}</p>
    <p><strong>Reference:</strong> {{ $message->reference }}</p>
    <p><strong>Date:</strong> {{ $message->created_at->format('M d, Y H:i') }}</p>
    <p><strong>Status:</strong> {{ $message->resolved ? '✔ Resolved' : '⏳ Open' }}</p>

    <p><strong>Category:</strong>
    <form action="{{ route('admin.emails.updateCategory', $message->id) }}" method="POST" class="inline">
        @csrf @method('PATCH')
        <select name="category" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
            <option value="">—</option>
            <option value="General" @selected($message->category == 'General')>General</option>
            <option value="Support" @selected($message->category == 'Support')>Support</option>
            <option value="Feedback" @selected($message->category == 'Feedback')>Feedback</option>
        </select>
    </form>
    </p>

    <hr>

    <p><strong>Message:</strong></p>
    <div class="bg-gray-100 p-4 rounded whitespace-pre-line">{{ $message->message }}</div>

    <form action="{{ route('admin.emails.toggleResolved', $message->id) }}" method="POST" class="mt-6">
        @csrf @method('PATCH')
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ $message->resolved ? 'Mark as Unresolved' : 'Mark as Resolved' }}
        </button>
    </form>
</div>
@endsection