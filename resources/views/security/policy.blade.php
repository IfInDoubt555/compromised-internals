@extends('layouts.app')

@section('content')
<div class="prose max-w-4xl mx-auto text-gray-800 mt-10 mb-10 text-lg leading-relaxed bg-white/45 backdrop-blur-md rounded-xl shadow-xl p-6">
    <h1 class="text-3xl font-bold mb-6">Responsible Disclosure Policy</h1>

    <p class="mb-4">
        At Compromised Internals, we take security seriously. If you discover a vulnerability in our site or services, we ask that you responsibly disclose it to us so we can take appropriate action to fix it quickly.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Reporting a Vulnerability</h2>
    <p class="mb-4">
        Please send an email to <a href="mailto:no-reply@compromisedinternals.com" class="text-blue-600 underline">
            no-reply@compromisedinternals.com
        </a>
        with detailed information including:
    </p>
    <ul class="list-disc list-inside mb-4">
        <li>A description of the vulnerability</li>
        <li>Steps to reproduce it (including URLs or sample payloads)</li>
        <li>Any potential impact</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">What You Should NOT Do</h2>
    <ul class="list-disc list-inside mb-4">
        <li>Do not perform denial-of-service attacks</li>
        <li>Do not access or modify data that isn't yours</li>
        <li>Do not share details of the vulnerability publicly before we’ve fixed it</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">Acknowledgments</h2>
    <p class="mb-4">
        If you report a valid issue and follow this policy, we’ll credit you on our Hall of Fame page (if desired).
    </p>

    <p class="mt-6 text-sm text-gray-500">
        Last updated: {{ now()->format('F j, Y') }}<br>
        See our <a href="{{ route('security.hof') }}" class="text-blue-600 hover:underline">Security Hall of Fame</a>.
    </p>
</div>
@endsection