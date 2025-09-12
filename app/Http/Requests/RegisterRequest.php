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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', new NoBannedWords],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],

            // Require at least one captcha token field
            'g-recaptcha-response' => ['required_without:recaptcha_token', 'string'],
            'recaptcha_token'      => ['required_without:g-recaptcha-response', 'string'],

            // Synthetic rule: validate with Google
            'captcha' => [function ($attribute, $value, $fail) {
                if (!config('services.recaptcha.secret')) {
                    return; // fail-closed only if you want, but here skip if not configured
                }

                $token = $this->input('g-recaptcha-response') ?: $this->input('recaptcha_token');
                if (!$token) {
                    return $fail('Captcha token is missing.');
                }

                $resp = Http::asForm()->post(
                    'https://www.google.com/recaptcha/api/siteverify',
                    [
                        'secret'   => config('services.recaptcha.secret'),
                        'response' => $token,
                        'remoteip' => $this->ip(),
                    ]
                )->json();

                if (!($resp['success'] ?? false)) {
                    return $fail('Captcha failed: ' . (($resp['error-codes'][0] ?? 'unknown')));
                }

                // Optional stricter checks for v3
                if (($resp['action'] ?? 'register') !== 'register') {
                    return $fail('Invalid captcha action.');
                }
                if (($resp['score'] ?? 0) < (float) config('services.recaptcha.threshold', 0.5)) {
                    return $fail('Suspicious activity detected. Please try again.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required_without' => 'Captcha verification is required.',
            'recaptcha_token.required_without'      => 'Captcha verification is required.',
        ];
    }
}