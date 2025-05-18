<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row items-center justify-center bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 overflow-hidden px-4 sm:px-6 lg:px-8">

        <!-- Left Image (Hidden on Mobile) -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img src="{{ asset('images/reg-left-image.png') }}" alt="Sno-Drift Attack" class="h-full w-full object-cover mask-fade-left" />
        </div>

        <!-- Register Box -->
        <div class="w-full px-4 sm:px-6 md:px-0 max-w-none sm:max-w-lg lg:max-w-xl bg-white shadow-2xl rounded-2xl p-8 sm:p-12 mx-auto my-10 border border-gray-100 z-10 transition-shadow duration-300">
            <div class="text-center mb-6">
                <div class="p-4 mb-4 rounded-lg border border-yellow-400 bg-yellow-100 text-yellow-900 shadow-md text-base sm:text-sm">
                    ⚠️ <strong>Testing Notice:</strong> You may now register with a real email…
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800">Join Compromised Internals</h2>
                <p class="mt-2 text-base sm:text-lg text-gray-500">Be part of the rally revolution 🚗💨</p>
            </div>

            @if ($errors->has('recaptcha'))
            <div class="text-red-600 text-sm mb-4 text-center">
                {{ $errors->first('recaptcha') }}
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                <div>
                    <x-input-label for="name" value="Name" class="text-base sm:text-lg" />
                    <x-text-input id="name" class="block w-full mt-1 text-base sm:text-lg" type="text" name="name" :value="old('name')" required autofocus style="height:3rem" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" class="text-base sm:text-lg" />
                    <x-text-input id="email" class="block w-full mt-1 text-base sm:text-lg" type="email" name="email" :value="old('email')" required style="height:3rem" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" class="text-base sm:text-lg" />
                    <x-text-input id="password" class="block w-full mt-1 text-base sm:text-lg" type="password" name="password" required style="height:3rem" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" class="text-base sm:text-lg" />
                    <x-text-input id="password_confirmation" class="block w-full mt-1 text-base sm:text-lg" type="password" name="password_confirmation" required style="height:3rem" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm" />
                </div>

                <div class="flex items-center justify-between text-base">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                        Already registered?
                    </a>
                </div>

                <x-primary-button class="w-full py-3 text-lg">
                    {{ __('Register') }}
                </x-primary-button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-blue-600 text-sm hover:underline">
                    ← Back to Home
                </a>
            </div>
        </div>

        <!-- Right Image (Hidden on Mobile) -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img src="{{ asset('images/reg-right-image.png') }}" alt="Forest Push" class="h-full w-full object-cover mask-fade-right" />
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector("form[action='{{ route('register') }}']");
            form.addEventListener('submit', e => {
                e.preventDefault();
                grecaptcha.ready(() => {
                    grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", {
                            action: 'register'
                        })
                        .then(token => {
                            document.getElementById('recaptcha_token').value = token;
                            setTimeout(() => form.submit(), 100);
                        });
                });
            });
        });
    </script>
    @endpush
</x-guest-layout>