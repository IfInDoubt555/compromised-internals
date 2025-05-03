<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email | {{ config('app.name') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
        async function checkVerified() {
            const res = await fetch('/api/user');
            const data = await res.json();
            if (data.email_verified_at) window.close();
        }
        window.addEventListener('load', () => {
            setTimeout(checkVerified, 3000);
        });
    </script>
</head>
<body class="bg-gradient-to-br from-gray-950 to-gray-900 text-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-gray-900 border border-red-600 rounded-2xl shadow-2xl p-10 w-full max-w-lg text-center">
        
        <div class="flex items-center justify-center mb-4">
            <span class="text-4xl">📬</span>
        </div>

        <h1 class="text-3xl font-bold mb-2">Verify Your Email</h1>
        <p class="text-gray-400 text-sm mb-6 leading-relaxed">
            A verification link has been sent to your inbox.<br>
            Click it to activate your account and unlock all features.
        </p>

        @if (session('status') === 'verification-link-sent')
            <div class="bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4 shadow">
                ✅ A new verification link has been sent!
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
            @csrf
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded text-white font-semibold tracking-wide transition">
                Resend Email
            </button>
        </form>

        <a href="{{ route('home') }}"
           class="text-sm text-gray-400 hover:text-white transition underline underline-offset-2">
            ← Back to Home
        </a>
    </div>
</body>
</html>
