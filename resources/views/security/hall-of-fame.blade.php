@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Security Hall of Fame</h1>

    <p class="mb-4">We thank the following individuals for reporting valid security issues and helping improve Compromised Internals.</p>

    <ul class="list-disc list-inside text-lg space-y-2">
        <li><strong>@ShadowSpecter</strong> – Reported XSS in blog search (April 2025)</li>
        <li><strong>RedLeaf</strong> – Found misconfigured CORS headers (May 2025)</li>
    </ul>
</div>
@endsection