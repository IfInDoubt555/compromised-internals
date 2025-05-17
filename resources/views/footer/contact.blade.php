@extends('layouts.app')

@push('head')
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endpush

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-center mb-6">📬 Contact</h1>

    @if ($errors->has('recaptcha'))
    <div class="mt-4 mb-6 rounded bg-red-100 dark:bg-red-900 border border-red-500 px-4 py-3 text-sm text-red-800 dark:text-red-100 font-semibold shadow animate-fade-in">
        ⚠️ {{ $errors->first('recaptcha') }}
    </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6 bg-white dark:bg-gray-500 p-6 rounded-xl shadow">
        @csrf
        <input type="hidden" name="recaptcha_token" id="recaptcha_token">

        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mt-4 mb-6 rounded shadow-md text-sm">
            <strong>Heads up!</strong> This site is in early testing. If you spot any bugs or have suggestions, please use this form to let me know! You don't have to use real information for the NAME and EMAIL fields below. test@test.com for the email field is fine.
        </div>

        <div>
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                class="w-full border border-gray-300 dark:border-gray-700 rounded px-4 py-2 dark:bg-gray-300" required>
            @error('name')
            <div class="mt-2 rounded-md bg-red-100 dark:bg-red-900 border border-red-500 px-4 py-2 text-sm font-semibold text-red-800 dark:text-red-100 shadow animate-fade-in">
                ⚠️ {{ $message }}
            </div>
            @enderror
        </div>

        <div>
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 dark:bg-gray-300" required>
            @error('email')
            <div class="mt-2 rounded-md bg-red-100 dark:bg-red-900 border border-red-500 px-4 py-2 text-sm font-semibold text-red-800 dark:text-red-100 shadow animate-fade-in">
                ⚠️ {{ $message }}
            </div>
            @enderror
        </div>

        <div>
            <label for="message" class="block font-semibold mb-1">Message</label>
            <textarea id="message" name="message" rows="6"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 dark:bg-gray-300"
                required>{{ old('message') }}</textarea>
            @error('message')
            <div class="mt-2 rounded-md bg-red-100 dark:bg-red-900 border border-red-500 px-4 py-2 text-sm font-semibold text-red-800 dark:text-red-100 shadow animate-fade-in">
                ⚠️ {{ $message }}
            </div>
            @enderror
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Send Message
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector("form[action='{{ route('contact.submit') }}']");

        if (!form) return;

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            if (typeof grecaptcha === 'undefined') {
                console.error('grecaptcha not loaded');
                return;
            }

            grecaptcha.ready(function() {
                grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", {
                    action: 'contact'
                }).then(function(token) {
                    document.getElementById('recaptcha_token').value = token;
                    form.submit();
                }).catch(function(err) {
                    console.error('reCAPTCHA error:', err);
                });
            });
        });
    });
</script>
@endpush