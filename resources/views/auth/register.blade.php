<x-guest-layout>
    <div class=" w-screen min-h-screen flex lg:flex-row flex-col items-center justify-center bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 px-0 sm:px-6 lg:px-8 overflow-hidden">
        <!-- Left Image (hidden on mobile) -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img
                src="{{ asset('images/reg-left-image.png') }}"
                alt="Sno-Drift Attack"
                class="h-full w-full object-cover mask-fade-left" />
        </div>

        <!-- Register Box -->
        <div class="w-full max-w-none sm:max-w-lg lg:max-w-xl bg-white shadow-2xl rounded-2xl p-8 sm:p-12 mx-auto my-10 border border-gray-100 z-10">
            <div class="text-center mb-6">
                <div class="p-4 mb-4 rounded-lg border border-yellow-400 bg-yellow-100 text-yellow-900 shadow-md text-sm animate-fade-in">
                    ⚠️ <strong>Testing Notice:</strong> You may now register with a real email…
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Join Compromised Internals</h2>
                <p class="text-sm text-gray-500 mt-2">Be part of the rally revolution 🚗💨</p>
            </div>

            @if ($errors->has('recaptcha'))
            <div class="text-red-600 text-sm mb-4">
                {{ $errors->first('recaptcha') }}
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

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

                <div class="flex items-center justify-between text-sm">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                        Already registered?
                    </a>
                </div>

                <x-primary-button class="w-full justify-center py-3 text-lg">
                    {{ __('Register') }}
                </x-primary-button>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('home') }}" class="text-blue-600 text-sm hover:underline">
                    ← Back to Home
                </a>
            </div>
        </div>

        <!-- Right Image (hidden on mobile) -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img
                src="{{ asset('images/reg-right-image.png') }}"
                alt="Forest Push"
                class="h-full w-full object-cover mask-fade-right" />
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