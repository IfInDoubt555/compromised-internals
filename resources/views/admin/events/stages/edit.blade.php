@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Stage — {{ $event->name }}</h1>

<form method="POST" action="{{ route('admin.events.stages.update', [$event,$stage]) }}" class="space-y-3 bg-white rounded shadow p-4">
  @csrf @method('PUT')
  {{-- same fields as create, prefilled with $stage --}}
  {{-- ... --}}
  <div>
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('admin.events.stages.index',$event) }}" class="btn">Cancel</a>
  </div>
</form>
@endsection