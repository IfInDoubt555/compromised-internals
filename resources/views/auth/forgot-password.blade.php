<x-guest-layout>
  <div class="max-w-md mx-auto my-12 rounded-2xl p-6 shadow
              bg-white/90 ring-1 ring-black/5
              dark:bg-stone-900/80 dark:ring-white/10">

    <div class="mb-4 text-sm text-gray-600 dark:text-stone-300">
      Forgot your password? No problem. Just let us know your email address and
      we will email you a password reset link that will allow you to choose a new one
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
      @csrf

      <!-- Email Address -->
      <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="flex items-center justify-end pt-2">
        <x-primary-button class="bg-red-600 hover:bg-red-700 dark:bg-rose-600 dark:hover:bg-rose-500">
          Email Password Reset Link
        </x-primary-button>
      </div>
    </form>
  </div>
</x-guest-layout>