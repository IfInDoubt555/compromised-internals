<x-guest-layout>
  @push('head')
    <title>Reset Password | Compromised Internals</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preload" as="image" href="{{ asset('images/compromised_internals_ci_logo_plain_cracked.png') }}">
  @endpush

  <div class="min-h-screen w-full grid place-items-center py-10
              bg-gradient-to-br from-stone-50 to-stone-200
              dark:from-stone-950 dark:to-stone-900">

    <section class="relative w-full max-w-md rounded-2xl p-8 sm:p-10 text-left
                    bg-white/90 ring-1 ring-black/5 shadow-xl backdrop-blur
                    dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10">

      {{-- Logo (optional but keeps auth screens consistent) --}}
      <div class="absolute -top-12 left-1/2 -translate-x-1/2">
        <img
          src="{{ asset('images/compromised_internals_ci_logo_plain_cracked.png') }}"
          alt="Compromised Internals"
          class="w-24 h-24 rounded-full ring-4 ring-white shadow-lg dark:ring-stone-800 object-cover"
          width="96" height="96"
          loading="eager" decoding="async" fetchpriority="high">
      </div>

      <h1 class="mt-14 text-2xl font-extrabold tracking-tight
                 text-stone-800 dark:text-stone-50">
        Forgot your password?
      </h1>

      <p class="mt-2 mb-6 text-sm leading-relaxed
                text-stone-600 dark:text-stone-300">
        No problem. Enter your email and we’ll send you a link to reset it.
      </p>

      {{-- Session Status --}}
      <x-auth-session-status class="mb-4"
        :status="session('status')" />

      <form method="POST" action="{{ route('password.email') }}"
            x-data="{sending:false}"
            @submit="sending=true"
            class="space-y-5">
        @csrf

        <div>
          <x-input-label for="email" :value="__('Email')" />
          <x-text-input
            id="email"
            name="email"
            type="email"
            :value="old('email')"
            autocomplete="email"
            inputmode="email"
            spellcheck="false"
            required
            autofocus
            class="block w-full mt-1
                   bg-white border-gray-300 placeholder-gray-500
                   dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
          <x-primary-button
            class="w-full justify-center px-6 py-2 rounded-full font-semibold
                   bg-red-600 hover:bg-red-700 text-white
                   dark:bg-rose-600 dark:hover:bg-rose-500"
            :disabled="true"
            x-bind:disabled="sending"
            x-bind:class="sending ? 'opacity-60 cursor-not-allowed' : ''">
            <span x-show="!sending">Email Password Reset Link</span>
            <span x-show="sending">Sending…</span>
          </x-primary-button>
        </div>
      </form>

      <div class="mt-4 text-center">
        <a href="{{ route('login') }}"
           class="text-sm underline underline-offset-4
                  text-stone-600 hover:text-red-600
                  dark:text-stone-300 dark:hover:text-rose-300">
          ← Back to Login
        </a>
      </div>
    </section>
  </div>
</x-guest-layout>