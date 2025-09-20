<?php

namespace App\Http\Requests;



class UpdatePostRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorize against the bound Post model (route-model-binding: {post})
        $post = $this->route('post');
        return (bool) $this->user()?->can('update', $post);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'        => 'sometimes|string|max:255',
            'body'         => 'sometimes|string',
            // if you’re updating the main image:
            'image'        => 'sometimes|file|image|max:5120', // 5MB
            'is_main'      => 'sometimes|boolean',
        ];
    }
}

