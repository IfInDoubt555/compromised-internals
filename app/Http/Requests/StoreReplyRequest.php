<?php

namespace App\Http\Requests;


use App\Http\Requests\Concerns\SanitizesInput;
use App\Rules\NoBannedWords;

class StoreReplyRequest extends BaseFormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    protected function prepareForValidation(): void
    {
        // Strip any HTML, keep markdown characters, normalize whitespace
        $this->sanitizePlain(['body']);
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000', new NoBannedWords],
        ];
    }
}

