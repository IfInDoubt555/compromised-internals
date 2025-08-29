@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">📨 Message from {{ $message->name }}</h1>

<div class="ci-admin-card space-y-4">
  <p><strong>Email:</strong> {{ $message->email }}</p>
  <p><strong>Reference:</strong> {{ $message->reference }}</p>
  <p><strong>Date:</strong> {{ $message->created_at->format('M d, Y H:i') }}</p>
  <p><strong>Status:</strong> {{ $message->resolved ? '✔ Resolved' : '⏳ Open' }}</p>

  <p class="flex items-center gap-3">
    <strong>Category:</strong>
    <form action="{{ route('admin.emails.updateCategory', $message->id) }}" method="POST" class="inline">
      @csrf @method('PATCH')
      <select name="category" onchange="this.form.submit()" class="ci-select text-sm">
        <option value="">—</option>
        <option value="General" @selected($message->category == 'General')>General</option>
        <option value="Support" @selected($message->category == 'Support')>Support</option>
        <option value="Feedback" @selected($message->category == 'Feedback')>Feedback</option>
        <option value="Security" @selected($message->category == 'Security')>Security</option>
        <option value="Media/Press" @selected($message->category == 'Media/Press')>Media/Press</option>
        <option value="Business Inquiry" @selected($message->category == 'Business Inquiry')>Business Inquiry</option>
        <option value="Shop & Orders" @selected($message->category == 'Shop & Orders')>Shop & Orders</option>
        <option value="Legal" @selected($message->category == 'Legal')>Legal</option>
        <option value="Feature Request" @selected($message->category == 'Feature Request')>Feature Request</option>
      </select>
    </form>
  </p>

  <hr class="border-t border-gray-200 dark:border-white/10">

  <p><strong>Message:</strong></p>
  <div class="ci-surface rounded p-4 whitespace-pre-line">
    {{ $message->message }}
  </div>

  {{-- Separate standalone form for status --}}
  <form action="{{ route('admin.emails.toggleResolved', $message->id) }}" method="POST" class="mt-6">
    @csrf
    @method('PATCH')
    <button class="ci-btn-primary">
      {{ $message->resolved ? 'Mark as Unresolved' : 'Mark as Resolved' }}
    </button>
  </form>

  {{-- Separate standalone form for archiving --}}
  <form action="{{ route('admin.emails.archive', $message->id) }}" method="POST" class="mt-2">
    @csrf
    @method('PATCH')
    <button class="ci-btn-ghost">
      {{ $message->archived ? 'Unarchive' : 'Archive' }}
    </button>
  </form>
</div>
@endsection