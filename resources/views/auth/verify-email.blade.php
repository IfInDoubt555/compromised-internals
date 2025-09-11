<x-guest-layout>
  @push('head')
    <title>Verify Email | Compromised Internals</title>
    <meta name="robots" content="noindex, nofollow">
    {{-- Preload the logo for a snappy paint --}}
    <link rel="preload" as="image" href="{{ asset('images/compromised_internals_ci_logo_plain_cracked.png') }}">
  @endpush>

  <div class="min-h-screen w-full grid place-items-center py-10
              bg-gradient-to-br from-stone-50 to-stone-200
              dark:from-stone-950 dark:to-stone-900">

    <section class="relative w-full max-w-md text-center rounded-2xl p-8 sm:p-10
                    bg-white/90 ring-1 ring-black/5 shadow-xl backdrop-blur
                    dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10">

      {{-- Logo --}}
      <div class="absolute -top-12 left-1/2 -translate-x-1/2">
        <img
          src="{{ asset('images/compromised_internals_ci_logo_plain_cracked.png') }}"
          alt="Compromised Internals"
          class="w-24 h-24 rounded-full ring-4 ring-white shadow-lg dark:ring-stone-800 object-cover"
          width="96" height="96"
          loading="eager" decoding="async" fetchpriority="high">
      </div>

      {{-- Content --}}
      <h1 class="mt-14 text-2xl font-extrabold tracking-tight
                 text-stone-800 dark:text-stone-50">
        Verify Your Email
      </h1>

      <p class="mt-2 mb-6 text-sm leading-relaxed
                text-stone-600 dark:text-stone-300">
        We’ve sent a verification link to your inbox. Click it to activate your account.
      </p>

      @if (session('status') === 'verification-link-sent')
        <div class="mb-4 inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium
                    bg-green-100 text-green-700 ring-1 ring-green-600/10
                    dark:bg-green-900/30 dark:text-green-300 dark:ring-green-400/20"
             role="status" aria-live="polite">
          <span>✅</span>
          <span>A new verification link has been sent.</span>
        </div>
      @endif

      {{-- Resend: disables after click to prevent spam --}}
      <form method="POST" action="{{ route('verification.send') }}"
            x-data="{sending:false}"
            @submit="sending=true"
            class="mb-2">
        @csrf
        <button type="submit"
                class="inline-flex items-center justify-center w-full px-6 py-2 rounded-full font-semibold
                       transition focus:outline-none focus:ring-2 focus:ring-offset-2
                       bg-red-600 text-white hover:bg-red-700
                       dark:bg-rose-600 dark:hover:bg-rose-500 dark:focus:ring-rose-400"
                :class="sending && 'opacity-60 cursor-not-allowed'"
                :disabled="sending">
          <span x-show="!sending">Resend Email</span>
          <span x-show="sending">Sending…</span>
        </button>
      </form>

      <a href="{{ route('home') }}"
         class="inline-block mt-3 text-sm underline underline-offset-4
                text-stone-600 hover:text-red-600
                dark:text-stone-300 dark:hover:text-rose-300">
        ← Back to Home
      </a>
    </section>
  </div>
</x-guest-layout>