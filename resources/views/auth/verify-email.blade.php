<x-guest-layout>
    <div class="min-h-screen bg-gray-950 flex flex-col justify-center items-center px-4">
        <div class="bg-gray-900 border border-red-600 rounded-2xl shadow-xl p-8 max-w-md w-full text-center relative">
            {{-- Skull Logo --}}
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 rounded-full overflow-hidden bg-gray-800 border-2 border-red-600">
                <img src="{{ asset('images/skull-logo.png') }}" alt="Logo" class="w-full h-full object-cover" />
            </div>

            {{-- Main Content --}}
            <h1 class="mt-12 text-3xl font-bold text-white">Verify Your Email</h1>
            <p class="text-sm text-gray-400 mt-2 mb-6 leading-relaxed">
                A verification link has been sent to your inbox.<br>
                Click it to activate your account and unlock all features.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="mb-4 text-sm text-green-500 font-semibold">
                    ✅ A new verification link has been sent!
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold transition tracking-wide"
                >
                    Resend Email
                </button>
            </form>

            <a
                href="{{ route('home') }}"
                class="text-sm text-gray-500 hover:text-white underline underline-offset-2 transition"
            >
                ← Back to Home
            </a>
        </div>
    </div>
</x-guest-layout>