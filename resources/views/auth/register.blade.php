<x-guest-layout>
    <div class="min-h-screen flex items-center justify-between bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 overflow-hidden px-6">

        <!-- Left Image -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img src="{{ asset('images/reg-left-image.png') }}"
                alt="Sno-Drift Attack"
                class="h-full w-full object-cover mask-fade-left" />
        </div>

        <!-- Register Box -->
        <div class="flex-none w-full max-w-md bg-white shadow-2xl rounded-2xl p-8 space-y-6 border border-gray-100 z-10 transition-all duration-300 hover:shadow-[0_0_60px_rgba(255,0,0,0.15)]">
            <div class="text-center">
                <div class="mt-4 p-4 rounded-lg border border-yellow-400 bg-yellow-100 text-yellow-900 dark:bg-yellow-900 dark:text-yellow-100 shadow-md text-sm animate-fade-in">
                    ⚠️ <strong>Testing Notice:</strong><br>
                    You may use any email address to register, including fake ones, thanks to MailCatcher on this dev environment.
                    <br class="hidden sm:block mt-1" />
                    🧹 <strong>Note:</strong> All test accounts and data are automatically <span class="font-semibold">purged every 24 hours</span>.
                    <br class="hidden sm:block mt-1" />
                    🔐 <strong>Verification:</strong> During testing, accounts must be manually verified. Please <a href="{{ route('contact') }}" class="underline font-medium text-blue-700 dark:text-blue-300">reach out here</a> if you don’t receive access shortly after signing up.
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Join Compromised Internals</h2>
                <p class="text-sm text-gray-500 mt-2">Be part of the rally revolution 🚗💨</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between text-sm text-gray-600">
                    <a href="{{ route('login') }}" class="hover:underline text-blue-600">Already registered?</a>
                </div>

                <x-primary-button class="w-full text-center justify-center">
                    {{ __('Register') }}
                </x-primary-button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-blue-600 text-sm hover:underline">← Back to Home</a>
            </div>
        </div>

        <!-- Right Image -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img src="{{ asset('images/reg-right-image.png') }}"
                alt="Forest Push"
                class="h-full w-full object-cover mask-fade-right" />
        </div>
    </div>
</x-guest-layout>