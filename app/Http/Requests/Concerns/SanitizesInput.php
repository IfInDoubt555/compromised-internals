<?php

namespace App\Http\Requests\Concerns;

trait SanitizesInput
{
    /**
     * Strip tags and normalize whitespace for plain text fields.
     * Pass an array of keys you want to sanitize.
     */
    protected function sanitizeStrings(array $keys): void
    {
        $data = $this->all();

        foreach ($keys as $key) {
            if (!array_key_exists($key, $data) || $data[$key] === null) continue;

            // basic normalization for plain text inputs
            $value = is_string($data[$key]) ? $data[$key] : (string)$data[$key];
            $value = preg_replace('/\s+/u', ' ', $value); // squash whitespace
            $value = trim(strip_tags($value));            // strip HTML tags
            $data[$key] = $value;
        }

        $this->replace($data);
    }
}