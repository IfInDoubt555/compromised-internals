<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\SanitizesInput;
use App\Models\Thread;

class StoreThreadRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        // Let policy decide; falls back to simple auth check if no policy.
        return $this->user()?->can('create', Thread::class) ?? $this->user()?->exists();
    }

    protected function prepareForValidation(): void
    {
        // Plain text only; keep Markdown symbols in body, strip any HTML.
        $this->sanitizePlain(['title', 'slug', 'body']);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'slug'  => ['nullable', 'string', 'max:180'],
            'body'  => ['required', 'string', 'max:20000'],
        ];
    }
}