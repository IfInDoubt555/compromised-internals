<x-guest-layout>
  @push('head')
    <title>Register | Compromised Internals</title>
    <meta name="description" content="Join Compromised Internals to share your rally stories, comment on posts, and connect with the motorsport community.">
    <meta name="robots" content="noindex, nofollow">
  @endpush

  <div class="w-screen min-h-screen flex lg:flex-row flex-col items-center justify-center px-0 sm:px-6 lg:px-8 overflow-hidden
              bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300
              dark:from-stone-950 dark:via-stone-900 dark:to-stone-950">

    {{-- Left image --}}
    <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
      <img src="{{ asset('images/reg-left-image.png') }}" alt="Sno-Drift Attack"
           class="h-full w-full object-cover mask-fade-left brightness-90 contrast-110" />
    </div>

    {{-- Auth card --}}
    <div class="w-full max-w-none sm:max-w-lg lg:max-w-xl mx-auto my-10 rounded-2xl p-8 sm:p-12 z-10
                bg-white/90 ring-1 ring-black/5 shadow-xl backdrop-blur
                dark:bg-stone-900/80 dark:ring-white/10">
      <div class="text-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-stone-100">Join Compromised Internals</h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-stone-400">Be part of the rally revolution 💨</p>
      </div>

      @if ($errors->has('recaptcha'))
        <div class="text-red-600 dark:text-rose-300 text-sm mb-4">
          {{ $errors->first('recaptcha') }}
        </div>
      @endif>

      <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="recaptcha_token" id="recaptcha_token">

        <div>
          <x-input-label for="name" value="Name" />
          <x-text-input
            id="name"
            name="name"
            type="text"
            :value="old('name')"
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

    {{-- Right image --}}
    <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
      <img src="{{ asset('images/reg-right-image.png') }}" alt="Forest Push"
           class="h-full w-full object-cover mask-fade-right brightness-90 contrast-110" />
    </div>
  </div>

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('register-form');
      const tokenInput = document.getElementById('recaptcha_token');
      if (!form) return;

      form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (typeof grecaptcha === 'undefined') return form.submit();

        grecaptcha.ready(function () {
          grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", { action: 'register' })
            .then(function (token) {
              tokenInput.value = token;
              if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
              } else {
                form.submit();
              }
            })
            .catch(console.error);
        });
      });
    });
  </script>
  @endpush
</x-guest-layout>