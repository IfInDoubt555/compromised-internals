<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class SlugService
{
    /**
     * Generate a unique slug for a model's table on a given column.
     *
     * @param class-string<Model> $modelClass  Eloquent model class (e.g., \App\Models\Post::class)
     */
    public static function generate(
        string $modelClass,
        string $base,
        string $column = 'slug',
        ?int $ignoreId = null
    ): string {
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = Str::random(8);
        }

        $candidate = $slug;
        $i = 2;

        /** @var Builder<Model> $q */
        $q = $modelClass::query();

        while (self::exists($q, $column, $candidate, $ignoreId)) {
            $candidate = $slug . '-' . $i;
            $i++;
        }

        return $candidate;
    }

    /**
     * @param Builder<Model> $query
     */
    private static function exists(
        Builder $query,
        string $column,
        string $value,
        ?int $ignoreId = null
    ): bool {
        $query = $query->where($column, $value);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }
}