@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto ci-admin-card">
  <h1 class="text-2xl font-bold mb-4">➕ Add New Rally Event</h1>

  <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-5">
    @csrf

    <div>
      <label for="championship" class="block text-sm font-medium mb-1">Championship</label>
      <select id="championship" name="championship" class="ci-select">
        <option value="">Select…</option>
        <option value="WRC" @selected(old('championship')==='WRC')>WRC</option>
        <option value="ARA" @selected(old('championship')==='ARA')>ARA</option>
        <option value="ERC" @selected(old('championship')==='ERC')>ERC</option>
      </select>
      @error('championship') <p class="ci-error">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Event Name</label>
      <input type="text" name="name" class="ci-input" required value="{{ old('name') }}">
      @error('name') <p class="ci-error">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Location</label>
      <input type="text" name="location" class="ci-input" required value="{{ old('location') }}">
      @error('location') <p class="ci-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Start Date</label>
        <input type="date" name="start_date" class="ci-input" required value="{{ old('start_date') }}">
        @error('start_date') <p class="ci-error">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">End Date</label>
        <input type="date" name="end_date" class="ci-input" required value="{{ old('end_date') }}">
        @error('end_date') <p class="ci-error">{{ $message }}</p> @enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Description</label>
      <textarea name="description" rows="4" class="ci-textarea">{{ old('description') }}</textarea>
      @error('description') <p class="ci-error">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="ci-btn-success">
      Save Event
    </button>
  </form>
</div>
@endsection