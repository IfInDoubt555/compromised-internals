<x-guest-layout>
  @push('head')
    <title>Login | Compromised Internals</title>
    <meta name="description" content="Log in to Compromised Internals to post, comment, and engage with the rally racing community.">
    <meta name="robots" content="noindex, nofollow">
  @endpush

  <div class="min-h-screen w-full overflow-hidden flex flex-col lg:flex-row items-center justify-center lg:justify-between
              bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300
              dark:from-stone-950 dark:via-stone-900 dark:to-stone-950">

    {{-- Left image --}}
    <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
      <img src="{{ asset('images/login-left.png') }}"
           alt="Night Stage Fire"
           class="h-full w-full object-cover mask-fade-left brightness-90 contrast-110" />
    </div>

    {{-- Auth card --}}
    <div class="w-full max-w-none sm:max-w-lg lg:max-w-xl mx-auto my-10 rounded-2xl p-8 sm:p-12 z-10
                bg-white/90 ring-1 ring-black/5 shadow-xl backdrop-blur
                hover:shadow-[0_0_60px_rgba(255,0,0,0.15)]
                dark:bg-stone-900/80 dark:ring-white/10 dark:hover:shadow-[0_0_60px_rgba(244,63,94,0.15)]">
      <div class="text-center mb-4">
        <h2 class="text-4xl sm:text-3xl font-bold text-gray-800 dark:text-stone-100">Welcome Back</h2>
        <p class="mt-2 text-base sm:text-sm text-gray-500 dark:text-stone-400">Glad to have you back on the rally stage</p>
      </div>

      @if ($errors->has('recaptcha'))
        <div class="text-red-600 dark:text-rose-300 text-sm mb-4">
          {{ $errors->first('recaptcha') }}
        </div>
      @endif

      <x-auth-session-status class="mb-6" :status="session('status')" />

      <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="recaptcha_token" id="recaptcha_token">

        {{-- Email --}}
        <div>
          <x-input-label for="email" value="Email" />
          <x-text-input
            id="email"
            name="email"
            type="email"
            :value="old('email')"
            required
            autofocus
            style="height:3rem"
            class="block w-full mt-1 text-base
                   bg-white border-gray-300 placeholder-gray-500
                   dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
        </div>

        {{-- Password --}}
        <div>
          <x-input-label for="password" value="Password" />
          <x-text-input
            id="password"
            name="password"
            type="password"
            required
            style="height:3rem"
            class="block w-full mt-1 text-base
                   bg-white border-gray-300 placeholder-gray-500
                   dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
        </div>

        <div class="flex items-center justify-between text-sm">
          <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" name="remember"
                   class="rounded border-gray-300 text-red-600 focus:ring-red-500
                          dark:bg-stone-800 dark:border-white/10" />
            <span class="ml-2 text-gray-700 dark:text-stone-300">Remember me</span>
          </label>

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="text-red-600 hover:underline dark:text-rose-300 dark:hover:text-rose-200">
              Forgot password?
            </a>
          @endif
        </div>

        <div class="text-center">
          <x-primary-button
            class="w-full justify-center py-3 text-lg
                   bg-red-600 hover:bg-red-700
                   dark:bg-rose-600 dark:hover:bg-rose-500">
            Log in
          </x-primary-button>
        </div>
      </form>

      <noscript class="mt-6 text-center text-red-600 dark:text-rose-300">
        ⚠️ JavaScript is required to log in. Please enable it in your browser.
      </noscript>

      <div class="mt-6 text-center">
        <a href="{{ route('home') }}"
           class="text-blue-600 hover:underline text-sm dark:text-sky-300 dark:hover:text-sky-200">
          ← Back to Home
        </a>
      </div>
    </div>

    {{-- Right image --}}
    <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
      <img src="{{ asset('images/login-right.png') }}"
           alt="Rally Forest Charge"
           class="h-full w-full object-cover mask-fade-right brightness-90 contrast-110" />
    </div>
  </div>

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('login-form');
      const tokenInput = document.getElementById('recaptcha_token');
      if (!form) return;

      form.addEventListener('submit', function (event) {
        if (tokenInput.value) return; // allow normal submit
        event.preventDefault();
        if (typeof grecaptcha === 'undefined') return form.submit();
        grecaptcha.ready(function () {
          grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", { action: 'login' })
            .then(function (token) { tokenInput.value = token; form.submit(); })
            .catch(console.error);
        });
      });
    });
  </script>
  @endpush
</x-guest-layout>