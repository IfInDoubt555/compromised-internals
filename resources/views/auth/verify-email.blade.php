<x-guest-layout>
    <div class="w-full min-h-screen bg-gradient-to-br from-gray-100 to-gray-300 flex flex-col justify-center items-center py-10">
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full text-center relative border-t-4 border-red-600">

            {{-- Skull Logo --}}
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                <img src="{{ asset('images/skull-logo.png') }}"
                    alt="Logo"
                    class="w-24 h-24 rounded-full border-4 border-white shadow-lg bg-white object-cover">
            </div>

            {{-- Main Content --}}
            <h1 class="mt-16 text-2xl font-extrabold text-gray-800">Verify Your Email</h1>
            <p class="text-sm text-gray-600 mt-2 mb-6 leading-relaxed">
                A verification link has been sent to your inbox.<br>
                Click it to activate your account and unlock all features.
            </p>

            @if (session('status') === 'verification-link-sent')
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                ✅ A new verification link has been sent!
            </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full font-semibold shadow transition">
                    Resend Email
                </button>
            </form>

            <a
                href="{{ route('home') }}"
                class="text-sm mt-4 inline-block text-gray-600 hover:text-red-600 underline underline-offset-2 transition">
                ← Back to Home
            </a>
        </div>
    </div>
</x-guest-layout>