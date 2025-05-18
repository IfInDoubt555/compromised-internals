<x-guest-layout>
    <div class="w-full max-w-none sm:max-w-lg lg:max-w-xl bg-white rounded-2xl shadow-xl p-6 sm:p-12">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">Welcome Back</h2>
            <p class="mt-2 text-gray-500">Glad to have you back on the rally stage 🏁</p>
        </div>

        {{-- Recaptcha error --}}
        @if($errors->has('recaptcha'))
        <div class="text-red-600 mb-4 text-sm">
            {{ $errors->first('recaptcha') }}
        </div>
        @endif

        <x-auth-session-status class="mb-6" :status="session('status')" />

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="recaptcha_token" id="recaptcha_token">

            {{-- Email --}}
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input
                    id="email"
                    class="block w-full mt-1 text-base"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    style="height:3rem" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
            </div>

            {{-- Password --}}
            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input
                    id="password"
                    class="block w-full mt-1 text-base"
                    type="password"
                    name="password"
                    required
                    style="height:3rem" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
            </div>

            {{-- Remember & Forgot --}}
            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                    <span class="ml-2">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-red-600 hover:underline">
                    Forgot password?
                </a>
                @endif
            </div>

            {{-- Submit --}}
            <x-primary-button class="w-full py-3 text-base">
                {{ __('Log in') }}
            </x-primary-button>
        </form>

        {{-- Back link --}}
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline text-sm">
                ← Back to Home
            </a>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector("form[action='{{ route('login') }}']");
            form.addEventListener('submit', e => {
                e.preventDefault();
                grecaptcha.ready(() => {
                    grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", {
                            action: 'login'
                        })
                        .then(token => {
                            document.getElementById('recaptcha_token').value = token;
                            setTimeout(() => form.submit(), 50);
                        });
                });
            });
        });
    </script>
    @endpush
</x-guest-layout>