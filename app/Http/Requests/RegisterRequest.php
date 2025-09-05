<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\NoBannedWords;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\ValidationRule|array|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new NoBannedWords()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new NoBannedWords()],
            'password' => ['required', 'confirmed', Password::defaults()],
            'recaptcha_token' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! (bool) config('services.recaptcha.enabled')) {
                        return; // Skip reCAPTCHA in local/dev
                    }

                    $response = Http::asForm()->post(
                        'https://www.google.com/recaptcha/api/siteverify',
                        [
                            'secret'   => (string) config('services.recaptcha.secret_key'),
                            'response' => (string) $value,
                            'remoteip' => (string) $this->ip(),
                        ]
                    );

                    /** @var array<string, mixed> $data */
                    $data = $response->json();

                    if (! ($data['success'] ?? false)) {
                        $fail('reCAPTCHA failed: ' . (string) ($data['error-codes'][0] ?? 'unknown error'));
                        return;
                    }

                    if (($data['score'] ?? 1.0) < 0.5) {
                        $fail('Suspicious activity detected. Please try again.');
                    }
                },
            ],
        ];
    }
}