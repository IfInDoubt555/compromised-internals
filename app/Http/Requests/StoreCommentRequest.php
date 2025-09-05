<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\NoBannedWords;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, list<string|ValidationRule|array>>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000', new NoBannedWords()],
        ];
    }
}