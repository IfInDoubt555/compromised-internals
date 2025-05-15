<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoBannedWords implements Rule
{
    public function passes($attribute, $value)
    {
        $bannedWords = config('bannedwords.banned');

        foreach ($bannedWords as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $value)) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'Your input contains inappropriate language.';
    }
}