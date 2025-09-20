<?php

namespace App\Http\Requests;



class AddToCartRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        // Any authenticated user can add/update cart
        // If you allow guests, return true unconditionally.
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
            'size'     => ['nullable', 'string', 'max:50'],
            'color'    => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Optional: sanitize input before validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'quantity' => (int) $this->input('quantity', 1),
            'size'     => $this->filled('size') ? trim($this->input('size')) : null,
            'color'    => $this->filled('color') ? trim($this->input('color')) : null,
        ]);
    }
}

