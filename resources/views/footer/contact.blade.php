@extends('layouts.app')

@push('head')
    <script
        src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"
        async defer>
    </script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="ci-card py-4 mb-6 text-center">
        <h1 class="ci-title-xl">Contact</h1>
        <p class="ci-body mt-2">
            Questions, ideas, or bug reports? Send a quick note below.
        </p>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="ci-alert mb-6" role="status">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('recaptcha'))
        <div class="ci-alert ci-alert-warn mb-6" role="alert">
            ⚠️ {{ $errors->first('recaptcha') }}
        </div>
    @endif

    {{-- Form --}}
    <form
        id="contact-form"
        action="{{ route('contact.submit') }}"
        method="POST"
        class="ci-card p-6 sm:p-8 space-y-6"
        novalidate
    >
        @csrf
        <input type="hidden" name="recaptcha_token" id="recaptcha_token">

        {{-- Name + Email side-by-side on desktop --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block font-semibold mb-1">Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    class="ci-input"
                    placeholder="Your name"
                    autocomplete="name"
                    autofocus
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                >
                @error('name')
                    <p id="name-error" class="ci-error mt-1">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    class="ci-input"
                    placeholder="you@example.com"
                    autocomplete="email"
                    inputmode="email"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >
                @error('email')
                    <p id="email-error" class="ci-error mt-1">⚠️ {{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Message --}}
        <div>
            <label for="message" class="block font-semibold mb-1">Message</label>
            <textarea
                id="message"
                name="message"
                rows="7"
                required
                class="ci-textarea"
                placeholder="Tell me what’s up…"
                @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
            >{{ old('message') }}</textarea>
            @error('message')
                <p id="message-error" class="ci-error mt-1">⚠️ {{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between gap-4 pt-2">
            <p class="ci-help">
                This site is protected by reCAPTCHA and the Google
                <a class="ci-link" href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacy Policy</a>
                and
                <a class="ci-link" href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</a>
                apply.
            </p>

            <button type="submit" class="ci-btn-primary">
                Send Message
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('contact-form');
  const tokenInput = document.getElementById('recaptcha_token');
  if (!form) return;

  form.addEventListener('submit', function (event) {
    if (tokenInput.value) return;     // already have a token

    event.preventDefault();           // get a token first
    if (typeof grecaptcha === 'undefined') {
      console.warn('grecaptcha not loaded; submitting anyway');
      return form.submit();
    }

    grecaptcha.ready(() => {
      grecaptcha.execute("{{ config('services.recaptcha.site_key') }}", { action: 'contact' })
        .then(token => { tokenInput.value = token; form.submit(); })
        .catch(() => { form.submit(); }); // fail-open so users aren’t blocked
    });
  });
});
</script>
@endpush