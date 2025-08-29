<x-guest-layout>
  <div class="max-w-md mx-auto my-12 rounded-2xl p-6 shadow
              bg-white/90 ring-1 ring-black/5
              dark:bg-stone-900/80 dark:ring-white/10">

    <div class="mb-4 text-sm text-gray-600 dark:text-stone-300">
      This is a secure area of the application. Please confirm your password before continuing
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
      @csrf

      <!-- Password -->
      <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" class="block mt-1 w-full"
                      type="password" name="password" required autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <div class="flex justify-end pt-2">
        <x-primary-button class="bg-red-600 hover:bg-red-700 dark:bg-rose-600 dark:hover:bg-rose-500">
          Confirm
        </x-primary-button>
      </div>
    </form>
  </div>
</x-guest-layout>