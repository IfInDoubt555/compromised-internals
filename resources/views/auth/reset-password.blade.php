<x-guest-layout>
  <div class="max-w-md mx-auto my-12 rounded-2xl p-6 shadow
              bg-white/90 ring-1 ring-black/5
              dark:bg-stone-900/75 dark:ring-white/10">
    <form method="POST" action="{{ route('password.store') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      {{-- Email --}}
      <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full"
                      type="email" name="email"
                      :value="old('email', $request->email)"
                      required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      {{-- Password --}}
      <div class="mt-4">
        <x-input-label for="password" value="Password" />
        <x-text-input id="password" class="block mt-1 w-full"
                      type="password" name="password"
                      required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      {{-- Confirm Password --}}
      <div class="mt-4">
        <x-input-label for="password_confirmation" value="Confirm Password" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                      type="password" name="password_confirmation"
                      required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      </div>

      <div class="flex items-center justify-end mt-6">
        <x-primary-button
          class="bg-red-600 hover:bg-red-700 dark:bg-rose-600 dark:hover:bg-rose-500">
          Reset Password
        </x-primary-button>
      </div>
    </form>
  </div>
</x-guest-layout>