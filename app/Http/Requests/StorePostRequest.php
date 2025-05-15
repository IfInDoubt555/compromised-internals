<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\NoBannedWords;


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
            'title' => ['required', 'max:255', new NoBannedWords],
            'body' => ['required', new NoBannedWords],
            'excerpt' => ['nullable', 'max:120', new NoBannedWords],
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:5120',
            'slug_mode' => ['required', 'in:auto,manual', new NoBannedWords],
            'slug' => 'nullable|string|unique:posts,slug,' . optional($this->post)->id,
        ];
    }
}
