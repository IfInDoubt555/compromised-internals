<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row items-center justify-center lg:justify-between bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 overflow-hidden w-full">

        <!-- Left Image (Hidden on Mobile) -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img src="{{ asset('images/login-left.png') }}" alt="Night Stage Fire" class="h-full w-full object-cover mask-fade-left" />
        </div>

        <!-- Login Box -->
        <div class="w-full max-w-sm sm:max-w-md md:max-w-lg bg-white shadow-2xl rounded-2xl px-6 py-8 mx-4 sm:mx-auto my-10 border border-gray-100 z-10 transition-all duration-300 hover:shadow-[0_0_60px_rgba(255,0,0,0.15)] text-base sm:text-sm">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-800">Welcome Back</h2>
                <p class="text-sm text-gray-500 mt-2">Glad to have you back on the rally stage 🏁</p>
            </div>

            @if ($errors->has('recaptcha'))
            <div class="text-red-600 text-sm mb-2">
                {{ $errors->first('recaptcha') }}
            </div>
            @endif

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me / Forgot -->
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" name="remember">
                        <span class="ms-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-red-600 hover:underline">Forgot password?</a>
                    @endif
                </div>

                <!-- Submit -->
                <x-primary-button class="w-full text-center justify-center">
                    {{ __('Log in') }}
                </x-primary-button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-blue-600 text-sm hover:underline">← Back to Home</a>
            </div>
        </div>

        <!-- Right Image (Hidden on Mobile) -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img src="{{ asset('images/login-right.png') }}" alt="Rally Forest Charge" class="h-full w-full object-cover mask-fade-right" />
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector("form[action='{{ route('login') }}']");

            if (!form) {
                console.error('Login form not found.');
                return;
            }

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                if (typeof grecaptcha === 'undefined') {
                    console.error('grecaptcha not defined!');
                    return;
                }

                grecaptcha.ready(function() {
                    grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", {
                        action: 'login'
                    }).then(function(token) {
                        const tokenField = document.getElementById('recaptcha_token');

                        if (tokenField) {
                            tokenField.value = token;
                            setTimeout(() => form.submit(), 50);
                        } else {
                            console.error('reCAPTCHA token field missing');
                        }
                    }).catch(function(err) {
                        console.error('reCAPTCHA execution failed', err);
                    });
                });
            });
        });
    </script>
    @endpush

</x-guest-layout>