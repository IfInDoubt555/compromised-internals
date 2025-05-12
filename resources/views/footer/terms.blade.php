@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">📜 Terms of Service</h1>

    <p class="mb-4">
        Welcome to Compromised Internals. By accessing or using our website, you agree to be bound by the terms outlined below. These terms apply to all visitors, users, and others who access or use our services.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">1. Acceptance of Terms</h2>
    <p class="mb-4">
        By using this site, you accept these terms in full. If you disagree with any part, please do not use our services.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">2. Use of Content</h2>
    <p class="mb-4">
        All content on this site is for informational and entertainment purposes only. You may not reuse or republish material without written consent.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">3. User Conduct</h2>
    <p class="mb-4">
        You agree not to engage in activity that disrupts or abuses the functionality or integrity of this site or its community.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">4. Changes to Terms</h2>
    <p class="mb-4">
        We reserve the right to modify these terms at any time. Your continued use after changes indicates acceptance.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">5. Contact</h2>
    <p>If you have any questions about these terms, please contact us via the <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">Contact page</a>.</p>
</div>
@endsection