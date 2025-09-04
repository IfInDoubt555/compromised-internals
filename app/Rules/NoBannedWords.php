<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class NoBannedWords implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $bannedWords = config('bannedwords.banned');

        foreach ($bannedWords as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $value)) {
                $fail('Your input contains inappropriate language.');
                return;
            }
        }
    }
}