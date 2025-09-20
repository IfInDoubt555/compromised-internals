<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Keys that should be treated as single-line inputs.
     * (Whitespace collapsed to single spaces; no newlines.)
     */
    protected array $singleLineKeys = [
        'title', 'slug', 'excerpt', 'status', 'board_id',
        'published_at', 'image_alt', 'tags', 'tag_list',
    ];

    /**
     * Keys that are definitely multi-line (Markdown/text areas).
     * (Preserve newlines; normalize CRLF to LF; trim trailing spaces.)
     */
    protected array $multiLineKeys = [
        'body', 'body_markdown', 'comment', 'comments', 'content',
        'description_long', 'notes',
    ];

    /* ---------- shared helpers ---------- */

    /** Decode \uXXXX (and bare uXXXX), HTML entities; remove zero-width & control chars (except \n and \t). */
    protected function sanitizeCommon(string $s): string
    {
        // Normalize line endings first (Windows/Mac -> LF)
        $s = str_replace(["\r\n", "\r"], "\n", $s);

        // Decode JSON-style \uXXXX and bare uXXXX
        $s = preg_replace_callback('/\\\\?u([0-9a-fA-F]{4})/', static function ($m) {
            $code = hexdec($m[1]);
            return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
        }, $s);

        // Decode HTML entities
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove zero-width & BOM
        $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s);

        // Strip other control chars but keep \n and \t (and we already normalized \r away)
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F\x{0080}-\x{009F}]/u', '', $s);

        return $s;
    }

    /** For single-line fields: collapse all whitespace, trim. */
    protected function sanitizeSingleLine(string $s): string
    {
        $s = $this->sanitizeCommon($s);
        return Str::of($s)->squish()->toString(); // collapses all whitespace (incl. newlines) to single spaces + trims
    }

    /** For multi-line fields: preserve newlines, trim trailing spaces, collapse excessive blank lines. */
    protected function sanitizeMultiLine(string $s): string
    {
        $s = $this->sanitizeCommon($s);

        // Trim trailing spaces before newline
        $s = preg_replace("/[ \t]+\n/", "\n", $s);

        // Optional: reduce 3+ blank lines to max 2 (keep paragraphs readable)
        $s = preg_replace("/\n{3,}/", "\n\n", $s);

        return trim($s);
    }

    /**
     * Sanitize all string inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        foreach ($data as $key => $value) {
            if (!is_string($value)) {
                // If nested arrays contain strings, you can handle them here if needed.
                continue;
            }

            if (in_array($key, $this->singleLineKeys, true)) {
                $data[$key] = $this->sanitizeSingleLine($value);
            } elseif (in_array($key, $this->multiLineKeys, true)) {
                $data[$key] = $this->sanitizeMultiLine($value);
            } else {
                // Default: be safe and treat unknown fields as multi-line to avoid accidental squishing.
                $data[$key] = $this->sanitizeMultiLine($value);
            }
        }

        // Replace the request payload with sanitized values.
        $this->replace($data);
    }
}