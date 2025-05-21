<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use App\Rules\NoBannedWords;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Everyone can hit the contact form
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255', new NoBannedWords],
            'email'           => ['required', 'string', 'email', 'max:255', new NoBannedWords],
            'message'         => ['required', 'string', 'min:10', new NoBannedWords],
            'recaptcha_token' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Skip in local/dev if you’ve toggled recaptcha off
                    if (!config('services.recaptcha.enabled')) {
                        return;
                    }

                    $response = Http::asForm()
                        ->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret'   => config('services.recaptcha.secret_key'),
                            'response' => $value,
                            'remoteip' => $this->ip(),
                        ])
                        ->throw()
                        ->json();

                    // Must be a successful call and score >= 0.5
                    if (!($response['success'] ?? false) || ($response['score'] ?? 0) < 0.5) {
                        $fail('Failed reCAPTCHA validation. Please try again.');
                    }
                },
            ],
        ];
    }
}