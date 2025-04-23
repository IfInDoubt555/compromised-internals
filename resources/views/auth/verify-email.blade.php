<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
        // Try to close tab if the user has verified
        async function checkVerified() {
            const res = await fetch('/api/user');
            const data = await res.json();
        
            if (data.email_verified_at) {
                window.close();
            }
        }
    
        window.addEventListener('load', () => {
            setTimeout(checkVerified, 2000); // Give time for verification to update
        });
    </script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-gray-900 rounded-2xl shadow-xl p-8 max-w-lg w-full text-center border border-red-600">
        <h1 class="text-3xl font-bold text-white mb-4">📬 Verify Your Email</h1>
        <p class="text-gray-300 mb-6">
            We’ve sent a verification link to your email.<br> Click the link to activate your account.
        </p>

        @if (session('status') === 'verification-link-sent')
            <div class="bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4">
                ✅ A new verification link has been sent!
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 transition px-6 py-2 rounded text-white font-medium uppercase tracking-wide">
                Resend Email
            </button>
        </form>

        <a href="{{ route('home') }}" class="mt-6 inline-block text-sm text-gray-400 hover:text-white transition">
            ← Back to Home
        </a>
    </div>
</body>
</html>
