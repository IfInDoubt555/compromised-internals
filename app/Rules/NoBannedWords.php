<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

final class NoBannedWords implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $banned = (array) config('bannedwords.banned', []);

        foreach ($banned as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', (string) $value)) {
                $fail('Your input contains inappropriate language.');
                return;
            }
        }
    }
}