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
            'recaptcha_token' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!config('services.recaptcha.enabled')) {
                    return; // ✅ Skip reCAPTCHA in local/dev
                }

                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => $this->ip(),
                ]);

                $data = $response->json();

                if (!($data['success'] ?? false)) {
                    $fail('reCAPTCHA failed: ' . ($data['error-codes'][0] ?? 'unknown error'));
                } elseif (($data['score'] ?? 1) < 0.5) {
                    $fail('Suspicious activity detected. Please try again.');
                }
            }],
        ];
    }
}