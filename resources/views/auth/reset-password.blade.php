<x-guest-layout>
  @push('head')
    <title>Reset Password | Compromised Internals</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preload" as="image" href="{{ asset('images/compromised_internals_ci_logo_plain_cracked.png') }}">
  @endpush

  <div class="min-h-screen w-full grid place-items-center py-10
              bg-gradient-to-br from-stone-50 to-stone-200
              dark:from-stone-950 dark:to-stone-900">

    <section class="relative w-full max-w-md rounded-2xl p-8 sm:p-10
                    bg-white/90 ring-1 ring-black/5 shadow-xl backdrop-blur
                    dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10">

      {{-- Logo --}}
      <div class="absolute -top-12 left-1/2 -translate-x-1/2">
        <img src="{{ asset('images/compromised_internals_ci_logo_plain_cracked.png') }}"
             alt="Compromised Internals"
             class="w-24 h-24 rounded-full ring-4 ring-white shadow-lg dark:ring-stone-800 object-cover"
             width="96" height="96" loading="eager" decoding="async" fetchpriority="high">
      </div>

      <h1 class="mt-14 text-2xl font-extrabold tracking-tight text-stone-800 dark:text-stone-50">
        Create a new password
      </h1>
      <p class="mt-2 mb-6 text-sm leading-relaxed text-stone-600 dark:text-stone-300">
        Enter your email and a new password to finish the reset.
      </p>

      <form method="POST" action="{{ route('password.store') }}"
            x-data="{sending:false, show:false, show2:false}"
            @submit="sending=true"
            class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div>
          <x-input-label for="email" value="Email" />
          <x-text-input id="email" name="email" type="email"
                        :value="old('email', $request->email)" required autofocus
                        autocomplete="username" inputmode="email" spellcheck="false"
                        class="block w-full mt-1
                               bg-white border-gray-300 placeholder-gray-500
                               dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
          <x-input-label for="password" value="Password" />
          <div class="relative">
            <x-text-input id="password" name="password"
                          x-bind:type="show ? 'text' : 'password'"
                          required autocomplete="new-password"
                          class="block w-full mt-1
                                 bg-white border-gray-300 placeholder-gray-500
                                 dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
            <button type="button" @click="show=!show"
                    class="absolute inset-y-0 right-3 my-auto text-xs text-stone-500 dark:text-stone-300">
              <span x-show="!show">Show</span><span x-show="show">Hide</span>
            </button>
          </div>
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div>
          <x-input-label for="password_confirmation" value="Confirm Password" />
          <div class="relative">
            <x-text-input id="password_confirmation" name="password_confirmation"
                          x-bind:type="show2 ? 'text' : 'password'"
                          required autocomplete="new-password"
                          class="block w-full mt-1
                                 bg-white border-gray-300 placeholder-gray-500
                                 dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500" />
            <button type="button" @click="show2=!show2"
                    class="absolute inset-y-0 right-3 my-auto text-xs text-stone-500 dark:text-stone-300">
              <span x-show="!show2">Show</span><span x-show="show2">Hide</span>
            </button>
          </div>
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit"
                class="w-full justify-center px-6 py-2 rounded-full font-semibold
                       bg-red-600 text-white hover:bg-red-700 transition
                       focus:outline-none focus:ring-2 focus:ring-offset-2
                       dark:bg-rose-600 dark:hover:bg-rose-500 dark:focus:ring-rose-400"
                :class="sending && 'opacity-60 cursor-not-allowed'"
                :disabled="sending">
          <span x-show="!sending">Reset Password</span>
          <span x-show="sending">Saving…</span>
        </button>
      </form>
    </section>
  </div>
</x-guest-layout>