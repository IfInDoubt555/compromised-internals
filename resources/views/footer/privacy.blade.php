@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">🔒 Privacy Policy</h1>

    <p class="mb-4">
        At Compromised Internals, your privacy is important to us. This Privacy Policy outlines how we collect, use, and protect your information when you interact with our website.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">1. Information We Collect</h2>
    <p class="mb-4">
        We may collect basic information such as your name, email address, and any other data you provide voluntarily through forms or comments.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">2. How We Use Your Information</h2>
    <p class="mb-4">
        Your data helps us improve our content, respond to inquiries, and manage site functionality. We do not sell or rent your personal information.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">3. Cookies</h2>
    <p class="mb-4">
        We use cookies to enhance user experience, monitor traffic patterns, and collect analytics. You can modify your browser settings to disable cookies.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">4. Data Security</h2>
    <p class="mb-4">
        We implement standard security measures to protect your information, but we cannot guarantee complete safety from unauthorized access.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">5. Third-Party Services</h2>
    <p class="mb-4">
        Some content or features may link to third-party sites. We are not responsible for the privacy practices of these external services.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">6. Changes to This Policy</h2>
    <p class="mb-4">
        We may update this policy from time to time. Changes will be posted here with an updated revision date.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">7. Contact</h2>
    <p>If you have any privacy-related questions, please reach out via the <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">Contact page</a>.</p>
</div>
@endsection