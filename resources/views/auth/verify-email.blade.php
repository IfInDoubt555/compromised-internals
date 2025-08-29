<x-guest-layout>
  <div class="w-full min-h-screen flex flex-col justify-center items-center py-10
              bg-gradient-to-br from-gray-100 to-gray-300
              dark:from-stone-950 dark:via-stone-900 dark:to-stone-950">
    <div class="relative max-w-md w-full text-center rounded-xl p-8 shadow-xl
                bg-white border-t-4 border-red-600 ring-1 ring-black/5
                dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10 dark:border-rose-500">
      {{-- Skull Logo --}}
      <div class="absolute -top-12 left-1/2 -translate-x-1/2">
        <img src="{{ asset('images/skull-logo.png') }}" alt="Logo"
             class="w-24 h-24 rounded-full border-4 border-white shadow-lg bg-white object-cover">
      </div>

      {{-- Main Content --}}
      <h1 class="mt-16 text-2xl font-extrabold text-gray-800 dark:text-stone-100">Verify Your Email</h1>
      <p class="mt-2 mb-6 text-sm leading-relaxed text-gray-600 dark:text-stone-300">
        A verification link has been sent to your inbox.<br>
        Click it to activate your account and unlock all features.
      </p>

      @if (session('status') === 'verification-link-sent')
        <div class="mb-4 px-4 py-2 rounded-full text-sm font-medium
                    bg-green-100 text-green-700
                    dark:bg-green-900/30 dark:text-green-300 dark:ring-1 dark:ring-green-400/20">
          ✅ A new verification link has been sent!
        </div>
      @endif

      <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
        @csrf
        <button type="submit"
                class="px-6 py-2 rounded-full font-semibold shadow transition
                       bg-red-600 text-white hover:bg-red-700
                       dark:bg-rose-600 dark:hover:bg-rose-500">
          Resend Email
        </button>
      </form>

      <a href="{{ route('home') }}"
         class="inline-block mt-4 text-sm underline underline-offset-2 transition
                text-gray-600 hover:text-red-600
                dark:text-stone-300 dark:hover:text-rose-300">
        ← Back to Home
      </a>
    </div>
  </div>
</x-guest-layout>