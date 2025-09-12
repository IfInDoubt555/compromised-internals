<x-guest-layout>
  @push('head')
    <title>Register | Compromised Internals</title>
    <meta name="description" content="Join Compromised Internals to share your rally stories, comment on posts, and connect with the motorsport community.">
    <meta name="robots" content="noindex, nofollow">
  @endpush>

  {{-- Full-bleed wrapper with responsive scenic rally background --}}
  <div class="relative min-h-screen w-full overflow-hidden flex items-center justify-center px-4">

    {{-- Responsive Background (Scenic Rally Road) --}}
    <picture class="absolute inset-0 -z-10 h-full w-full">
      {{-- Desktop --}}
      <source
        media="(min-width: 1024px)"
        type="image/webp"
        srcset="{{ asset('images/register-bg/register-bg-desktop-1920.webp') }} 1920w,
                {{ asset('images/register-bg/register-bg-desktop-2560.webp') }} 2560w,
                {{ asset('images/register-bg/register-bg-desktop-3840.webp') }} 3840w"
        sizes="100vw">

      {{-- Mobile --}}
      <source
        media="(max-width: 1023px)"
        type="image/webp"
        srcset="{{ asset('images/register-bg/register-bg-mobile-720.webp') }} 720w,
                {{ asset('images/register-bg/register-bg-mobile-1080.webp') }} 1080w,
                {{ asset('images/register-bg/register-bg-mobile-2160.webp') }} 2160w"
        sizes="100vw">

      <img src="{{ asset('images/register-bg/register-bg-desktop-1920.webp') }}"
           alt="Scenic rally road stretching into the horizon — your rally journey begins here"
           class="h-full w-full object-cover brightness-90 dark:brightness-75"
           loading="eager" decoding="async" fetchpriority="high">
    </picture>

    {{-- Optional subtle overlay to improve contrast over bright backgrounds --}}
    <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-white/0 via-white/0 to-white/10 dark:from-black/0 dark:via-black/20 dark:to-black/40"></div>

    {{-- Auth card --}}
    <div class="w-full max-w-xl mx-auto my-10 rounded-2xl p-8 sm:p-12 z-10
                bg-white/90 ring-1 ring-black/5 shadow-xl backdrop-blur
                transition-shadow
                hover:shadow-[0_0_60px_rgba(16,185,129,0.22)]
                focus-within:shadow-[0_0_60px_rgba(16,185,129,0.22)]
                dark:bg-stone-900/80 dark:ring-white/10
                dark:hover:shadow-[0_0_60px_rgba(52,211,153,0.25)]
                dark:focus-within:shadow-[0_0_60px_rgba(52,211,153,0.25)]">
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-stone-100">Join Compromised Internals</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-stone-400">Your rally journey starts here 💨</p>
      </div>

      {{-- show reCAPTCHA/captcha errors --}}
      @if ($errors->has('captcha'))
        <div class="text-red-600 dark:text-rose-300 text-sm mb-4">
          {{ $errors->first('captcha') }}
        </div>
      @endif

      <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-5" autocomplete="on" novalidate>
        @csrf

        {{-- Honeypot (kept in the DOM but visually hidden) --}}
        <div class="sr-only" aria-hidden="true">
          <label for="nickname">Nickname</label>
          <input type="text" name="nickname" id="nickname" tabindex="-1" autocomplete="off">
        </div>

        {{-- reCAPTCHA v3 token field (controller prefers this) --}}
        <input type="hidden" name="recaptcha_token" id="recaptcha_token">
        {{-- Also set v2-compatible name to cover both paths in controller --}}
        <input type="hidden" name="g-recaptcha-response" id="g_recaptcha_response">

        <div>
          <x-input-label for="name" value="Name" />
          <x-text-input
            id="name"
            name="name"
            type="text"
            :value="old('name')"
            autocomplete="name"
            spellcheck="false"
            required
            autofocus
            class="block w-full mt-1
                   bg-white border-gray-300 placeholder-gray-500
                   dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
          <x-input-label for="email" value="Email" />
          <x-text-input
            id="email"
            name="email"
            type="email"
            :value="old('email')"
            autocomplete="email"
            spellcheck="false"
            inputmode="email"
            required
            class="block w-full mt-1
                   bg-white border-gray-300 placeholder-gray-500
                   dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
          <x-input-label for="password" value="Password" />
          <x-text-input
            id="password"
            name="password"
            type="password"
            autocomplete="new-password"
            required
            class="block w-full mt-1
                   bg-white border-gray-300 placeholder-gray-500
                   dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
          <x-input-label for="password_confirmation" value="Confirm Password" />
          <x-text-input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            class="block w-full mt-1
                   bg-white border-gray-300 placeholder-gray-500
                   dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between text-sm">
          <a href="{{ route('login') }}"
             class="text-blue-600 hover:underline dark:text-sky-300 dark:hover:text-sky-200">
            Already registered?
          </a>
        </div>

        <div class="text-center">
          <button type="submit"
                  class="w-full px-6 py-3 rounded font-semibold transition
                         bg-red-600 text-white hover:bg-red-700
                         dark:bg-rose-600 dark:hover:bg-rose-500">
            Register
          </button>
        </div>
      </form>

      <noscript class="mt-6 text-center text-red-600 dark:text-rose-300">
        ⚠️ JavaScript is required to register. Please enable it in your browser.
      </noscript>

      <div class="text-center mt-6">
        <a href="{{ route('home') }}"
           class="text-blue-600 hover:underline text-sm dark:text-sky-300 dark:hover:text-sky-200">
          ← Back to Home
        </a>
      </div>
    </div>
  </div>

  @push('scripts')
    {{-- reCAPTCHA v3 --}}
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site') }}"  nonce="@cspNonce"></script>
    <script nonce="@cspNonce">
      document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('register-form');
        const v3 = '{{ config("services.recaptcha.site") }}';
        let submitting = false;

        if (!form) return;

        form.addEventListener('submit', async (e) => {
          if (submitting) return;
          if (typeof grecaptcha === 'undefined') return; // let backend fail-closed if misconfigured

          e.preventDefault();

          try {
            const token = await grecaptcha.execute(v3, { action: 'register' });
            const v3Field = document.getElementById('recaptcha_token');
            const v2Field = document.getElementById('g_recaptcha_response');
            if (v3Field) v3Field.value = token;
            if (v2Field) v2Field.value = token;
          } catch (err) {
            console.error('reCAPTCHA error:', err);
          }

          submitting = true;
          form.submit();
        });
      });
    </script>
  @endpush
</x-guest-layout>