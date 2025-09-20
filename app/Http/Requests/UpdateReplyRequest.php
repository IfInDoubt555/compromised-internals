<?php

namespace App\Http\Requests;


use App\Http\Requests\Concerns\SanitizesInput;
use App\Models\Reply;
use App\Rules\NoBannedWords;

class UpdateReplyRequest extends BaseFormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        /** @var Reply|null $reply */
        $reply = $this->route('reply');
        // Owner-only (you can swap to a policy later)
        return (bool) $this->user() && $reply && $this->user()->id === (int) $reply->user_id;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizePlain(['body']);
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000', new NoBannedWords],
        ];
    }
}

