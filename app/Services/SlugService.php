<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class SlugService
{
    /**
     * Generate a unique slug for a model's table on a given column.
     *
     * @param class-string<Model> $modelClass  Eloquent model class (e.g., \App\Models\Post::class)
     * @param string $base                     Base string to slugify
     * @param string $column                   Column name that stores the slug (default: 'slug')
     * @param int|null $ignoreId               Existing model id to ignore for uniqueness checks (for updates)
     */
    public static function generate(string $modelClass, string $base, string $column = 'slug', ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = Str::random(8);
        }

        $candidate = $slug;
        $i = 2;

        /** @var \Illuminate\Database\Eloquent\Builder $q */
        $q = $modelClass::query();

        while (self::exists($q, $column, $candidate, $ignoreId)) {
            $candidate = $slug . '-' . $i;
            $i++;
        }

        return $candidate;
    }

    private static function exists($query, string $column, string $value, ?int $ignoreId = null): bool
    {
        $query = $query->where($column, $value);
        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }
        return $query->exists();
    }
}