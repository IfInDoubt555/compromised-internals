<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoBannedWords;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // No auth required for contact form
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255', new NoBannedWords],
            'email'   => ['required', 'email'],
            'message' => ['required', 'string', 'min:10', new NoBannedWords],
        ];
    }
}