<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Support\Str;

trait SanitizesInput
{
    /**
     * Strip HTML tags and normalize ends while preserving Markdown.
     * - Keeps line breaks
     * - Trims edges
     * - Removes trailing spaces at line ends
     */
    protected function sanitizePlain(array $fields): void
    {
        $clean = [];

        foreach ($fields as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = (string) $this->input($field, '');

            // normalize newlines, keep them (Markdown-friendly)
            $value = str_replace(["\r\n", "\r"], "\n", $value);

            // remove HTML tags only; keep Markdown punctuation
            $value = strip_tags($value);

            // remove trailing spaces before newlines, and trim ends
            $value = preg_replace('/[ \t]+(?=\n)/', '', $value);
            $value = trim($value);

            $clean[$field] = $value;
        }

        if ($clean) {
            $this->merge($clean);
        }
    }

    /**
     * Ensure a lowercase-kebab slug; if blank, derive from another field.
     */
    protected function sanitizeSlug(string $slugField = 'slug', ?string $fromField = null, int $max = 180): void
    {
        $slug = (string) $this->input($slugField, '');

        if ($slug === '' && $fromField) {
            $source = (string) $this->input($fromField, '');
            $slug = Str::slug(Str::limit($source, $max, ''));
        } else {
            $slug = Str::slug(Str::limit($slug, $max, ''));
        }

        $this->merge([$slugField => $slug]);
    }
}