<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use App\Rules\NoBannedWords;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new NoBannedWords],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new NoBannedWords],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],

            // Accept either g-recaptcha-response (standard) or recaptcha_token (legacy)
            'g-recaptcha-response' => ['nullable', 'string'],
            'recaptcha_token' => ['nullable', 'string'],

            // Synthetic rule to validate whichever token was sent
            'recaptcha' => [function ($attribute, $value, $fail) {
                if (!config('services.recaptcha.enabled')) {
                    return; // skip in envs where captcha is disabled
                }

                // Prefer the standard key; fall back to legacy
                $token = $this->input('g-recaptcha-response') ?: $this->input('recaptcha_token');
                if (!$token) {
                    return $fail('reCAPTCHA token is missing.');
                }

                $resp = Http::asForm()->post(
                    'https://www.google.com/recaptcha/api/siteverify',
                    [
                        'secret'   => config('services.recaptcha.secret_key'),
                        'response' => $token,
                        'remoteip' => $this->ip(),
                    ]
                )->json();

                if (!($resp['success'] ?? false)) {
                    return $fail('reCAPTCHA failed: ' . (($resp['error-codes'][0] ?? 'unknown')));
                }

                // Optional: enforce action + score for v3
                if (($resp['action'] ?? 'register') !== 'register') {
                    return $fail('Invalid reCAPTCHA action.');
                }
                if (($resp['score'] ?? 0) < 0.5) {
                    return $fail('Suspicious activity detected. Please try again.');
                }
            }],
        ];
    }
}