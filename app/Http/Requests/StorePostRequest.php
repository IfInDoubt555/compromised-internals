<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();  
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'excerpt' => 'nullable|max:500',
            'body' => 'required',
            'image_path' => 'nullable|image|max:2048',
            'slug_mode' => 'required|in:auto,manual',
            'slug' => 'nullable|string|unique:posts,slug,' . optional($this->post)->id,
        ];
    }
}
