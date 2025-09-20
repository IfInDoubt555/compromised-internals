<?php

namespace App\Http\Requests;



class BaseFormRequest extends BaseFormRequest
{
    /**
     * Decode plain-text sequences like u2019 / u003E and surrogate pairs (e.g., ud83dudd17)
     * across ALL string inputs before validation/saving.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $this->decodePlainUnicode($value);
            }
        }

        $this->merge($data);
    }

    private function decodePlainUnicode(string $s): string
    {
        // 1) Surrogate pairs: ud83dudxxx -> emoji
        $s = preg_replace_callback('/u(d[89ab][0-9a-f]{2})u(d[c-f][0-9a-f]{2})/i', function ($m) {
            $hi = hexdec($m[1]);
            $lo = hexdec($m[2]);
            $cp = 0x10000 + (($hi - 0xD800) << 10) + ($lo - 0xDC00);
            return html_entity_decode('&#'.$cp.';', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }, $s) ?? $s;

        // 2) Single BMP units: u2019, u003E, u00EB, etc.
        $s = preg_replace_callback('/u([0-9a-f]{4})\b/i', function ($m) {
            return html_entity_decode('&#'.hexdec($m[1]).';', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }, $s) ?? $s;

        return $s;
    }
}

