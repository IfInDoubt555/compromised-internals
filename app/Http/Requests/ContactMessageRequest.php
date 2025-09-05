<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\NoBannedWords;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // everyone can hit the contact form
    }

    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\ValidationRule|array|string>>
     */
    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255', new NoBannedWords()],
            'email'           => ['required', 'string', 'email', 'max:255', new NoBannedWords()],
            'message'         => ['required', 'string', 'min:10', new NoBannedWords()],
            'recaptcha_token' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! (bool) config('services.recaptcha.enabled')) {
                        return; // Skip in local/dev
                    }

                    $resp = Http::asForm()->post(
                        'https://www.google.com/recaptcha/api/siteverify',
                        [
                            'secret'   => (string) config('services.recaptcha.secret_key'),
                            'response' => (string) $value,
                            'remoteip' => (string) $this->ip(),
                        ]
                    );

                    /** @var array<string, mixed> $data */
                    $data = $resp->json();

                    if (! ($data['success'] ?? false) || (float) ($data['score'] ?? 0.0) < 0.5) {
                        $fail('Failed reCAPTCHA validation. Please try again.');
                    }
                },
            ],
        ];
    }
}