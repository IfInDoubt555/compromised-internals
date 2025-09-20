<?php

namespace App\Http\Requests;


use App\Rules\NoBannedWords;
use Illuminate\Support\Str;

class StoreCommentRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000', new NoBannedWords],
        ];
    }

    /**
     * Sanitize the validated input before passing it to controllers.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('body')) {
            $clean = strip_tags($this->input('body'));   // strip HTML tags
            $clean = Str::of($clean)->squish();          // collapse whitespace
            $this->merge([
                'body' => $clean,
            ]);
        }
    }
}

