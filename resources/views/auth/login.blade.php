<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row items-center justify-center lg:justify-between bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 w-full overflow-hidden">

        <!-- Left Image (Hidden on Mobile) -->
        <div class="hidden lg:block flex-grow basis-[45%] h-[90vh]">
            <img src="{{ asset('images/login-left.png') }}" alt="Night Stage Fire" class="h-full w-full object-cover mask-fade-left" />
        </div>

        <!-- Login Box -->
        <div class="w-full max-w-none sm:max-w-lg lg:max-w-xl bg-white shadow-2xl rounded-2xl p-8 sm:p-12 mx-auto my-10 border border-gray-100 z-10 transition-shadow duration-300 hover:shadow-[0_0_60px_rgba(255,0,0,0.15)]">
            <div class="text-center mb-4">
                <h2 class="text-4xl sm:text-3xl font-bold text-gray-800">Welcome Back</h2>
                <p class="text-base sm:text-sm text-gray-500 mt-2">Glad to have you back on the rally stage 🏁</p>
            </div>

            @if ($errors->has('recaptcha'))
            <div class="text-red-600 text-sm mb-4">
                {{ $errors->first('recaptcha') }}
            </div>
            @endif

            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="block w-full mt-1 text-base" type="email" name="email" :value="old('email')" required autofocus style="height:3rem" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="block w-full mt-1 text-base" type="password" name="password" required style="height:3rem" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
                </div>

                <!-- Remember / Forgot -->
                <div class="flex items-center justify-between text-sm">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-red-600 hover:underline">Forgot password?</a>
                    @endif
                </div>

                <!-- Submit -->
                <x-primary-button class="w-full justify-center py-3 text-lg">
                    {{ __('Log in') }}
                </x-primary-button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-blue-600 hover:underline text-sm">← Back to Home</a>
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
            if (!form) return;
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                grecaptcha.ready(function() {
                    grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", {
                            action: 'login'
                        })
                        .then(function(token) {
                            document.getElementById('recaptcha_token').value = token;
                            setTimeout(() => form.submit(), 50);
                        });
                });
            });
        });
    </script>
    @endpush

</x-guest-layout>