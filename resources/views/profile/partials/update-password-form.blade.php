<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-stone-100">Update Password</h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-stone-400">Ensure your account is using a long, random password to stay secure</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="dark:[&_label]:text-stone-300 dark:[&_input]:bg-stone-800 dark:[&_input]:text-stone-100 dark:[&_input]:border-white/10 dark:[&_input::placeholder]:text-stone-500">
            <x-input
                name="current_password"
                type="password"
                label="Current Password"
                id="update_password_current_password"
                autocomplete="current-password"
            />
        </div>

        <div class="dark:[&_label]:text-stone-300 dark:[&_input]:bg-stone-800 dark:[&_input]:text-stone-100 dark:[&_input]:border-white/10 dark:[&_input::placeholder]:text-stone-500">
            <x-input
                name="new_password"
                type="password"
                label="New Password"
                id="update_password_password"
                autocomplete="new-password"
            />
        </div>

        <div class="dark:[&_label]:text-stone-300 dark:[&_input]:bg-stone-800 dark:[&_input]:text-stone-100 dark:[&_input]:border-white/10 dark:[&_input::placeholder]:text-stone-500">
            <x-input
                name="password_confirmation"
                type="password"
                label="Confirm Password"
                id="update_password_password_confirmation"
                autocomplete="new-password"
            />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl shadow hover:bg-blue-700 transition">
                💾 Save
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-stone-400">Saved</p>
            @endif
        </div>
    </form>
</section>