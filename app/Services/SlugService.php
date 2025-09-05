<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class SlugService
{
    /**
     * Generate a unique slug under a given table/column.
     *
     * Usage:
     *  - SlugService::generate('My Title')                           // table=posts, column=slug
     *  - SlugService::generate('My Title', 'posts')                  // explicit table
     *  - SlugService::generate('My Title', 'posts', 'custom_slug')   // explicit column
     *  - SlugService::generate('My Title', 'posts', 'slug', 123)     // ignore row id=123
     *
     * @param  string      $base      Human string to slugify
     * @param  string      $table     DB table name (defaults to 'posts')
     * @param  string      $column    Column to ensure uniqueness on (defaults to 'slug')
     * @param  int|null    $ignoreId  Primary key to exclude from uniqueness (assumes 'id' PK)
     */
    public static function generate(
        string $base,
        string $table = 'posts',
        string $column = 'slug',
        ?int $ignoreId = null
    ): string {
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = Str::random(8);
        }

        $candidate = $slug;
        $i = 2;

        while (self::exists($table, $column, $candidate, $ignoreId)) {
            $candidate = $slug . '-' . $i;
            $i++;
        }

        return $candidate;
    }

    /**
     * Back-compat helper if you prefer passing a model class.
     * Example: SlugService::generateForModel(\App\Models\Post::class, 'My Title', 'slug', 123)
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     */
    public static function generateForModel(
        string $modelClass,
        string $base,
        string $column = 'slug',
        ?int $ignoreId = null
    ): string {
        $model = new $modelClass();
        /** @var string $table */
        $table = $model->getTable();

        return self::generate($base, $table, $column, $ignoreId);
    }

    private static function exists(
        string $table,
        string $column,
        string $value,
        ?int $ignoreId = null
    ): bool {
        $q = DB::table($table)->where($column, $value);

        if ($ignoreId !== null) {
            $q->where('id', '!=', $ignoreId);
        }

        return $q->exists();
    }
}