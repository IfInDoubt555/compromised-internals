<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Normalize any odd “uXXXX” sequences, HTML entities, zero-width chars, etc.
     */
    protected function sanitizeString(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        // Normalize line endings
        $s = str_replace(["\r\n", "\r"], "\n", $value);

        // Decode JSON-style \uXXXX **and** bare uXXXX sequences
        $s = preg_replace_callback('/\\\\?u([0-9a-fA-F]{4})/', function ($m) {
            $code = hexdec($m[1]);                          // e.g. 2019
            return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
        }, $s);

        // Decode HTML entities (&amp; &quot; &#x2019; etc.)
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove zero-width & BOM chars
        $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s);

        // Strip other control chars but keep tabs/newlines
        $s = preg_replace('/\p{C}+/u', '', $s);

        // Collapse long runs of spaces (preserve newlines)
        $s = preg_replace("/[ \t\x{00A0}]{2,}/u", ' ', $s);

        return $s;
    }

    /**
     * Sanitize all string inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = $this->sanitizeString($value);
            }
        });

        $this->merge($data);
    }
}