<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class SlugService
{
    public static function generate($base, $ignoreId = null): string
    {
        $slug = Str::slug($base);
        $original = $slug;
        $counter = 1;

        $query = Post::where('slug', $slug);
        if ($ignoreId) $query->where('id', '!=', $ignoreId);

        while ($query->exists()) {
            $slug = $original . '-' . $counter++;
            $query = Post::where('slug', $slug);
            if ($ignoreId) $query->where('id', '!=', $ignoreId);
        }

        return $slug;
    }
}
