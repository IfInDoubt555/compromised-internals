<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\SanitizesInput;
use Illuminate\Validation\Rule;
use App\Models\Thread;

class UpdateThreadRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        /** @var Thread|null $thread */
        $thread = $this->route('thread');
        return $this->user()?->can('update', $thread ?? Thread::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizePlain(['title', 'slug', 'body']);

        // Only normalize slug if present; don't overwrite existing when omitted
        if ($this->filled('slug')) {
            $this->sanitizeSlug('slug');
        }
    }

    public function rules(): array
    {
        /** @var Thread|null $thread */
        $thread = $this->route('thread');

        return [
            'board_id' => ['required', 'exists:boards,id'],
            'title'    => ['required', 'string', 'min:3', 'max:160'],
            'slug'     => [
                'sometimes',
                'nullable',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('threads', 'slug')->ignore($thread?->id),
            ],
            'body'     => ['required', 'string', 'max:20000'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug must contain only lowercase letters, numbers, and dashes.',
        ];
    }
}