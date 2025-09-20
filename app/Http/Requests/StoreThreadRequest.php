<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesInput;
use App\Models\Thread;

class StoreThreadRequest extends BaseFormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        // Policy handles this; fallback to simple auth check
        return $this->user()?->can('create', Thread::class) ?? $this->user()?->exists();
    }

    protected function prepareForValidation(): void
    {
        // Keep Markdown, strip HTML
        $this->sanitizePlain(['title', 'slug', 'body']);
        // Make slug kebab; if blank, derive from title
        $this->sanitizeSlug('slug', 'title');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'slug'  => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'body'  => ['required', 'string', 'max:20000'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug must contain only lowercase letters, numbers, and dashes.',
        ];
    }
}

