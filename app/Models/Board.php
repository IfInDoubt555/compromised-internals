<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Board extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name','slug','icon','color','position','is_public','description'];

    /** Tailwind palette whitelist used for theming */
    public const TAILWIND_ALLOWED = [
        'slate','stone','red','orange','amber','yellow','lime','green',
        'emerald','teal','cyan','sky','blue','indigo','violet','purple',
        'fuchsia','pink','rose',
    ];

    public function accentButtonClasses(): string
    {
        $c = $this->color_token;
        return "border border-{$c}-400 text-{$c}-700 bg-{$c}-100 hover:bg-{$c}-200 " .
               "ring-1 ring-{$c}-500/20 " .
               "dark:border-{$c}-600 dark:text-{$c}-300 dark:bg-{$c}-950/40 dark:hover:bg-{$c}-900/50 " .
               "dark:ring-{$c}-400/20";
    }

    /** @var array<string,string> */
    protected $casts = [
        'is_public' => 'boolean',
        'position'  => 'integer',
    ];

    // Optional: lets boards.index show counts without extra queries
    /** @var list<string> */
    protected $withCount = ['threads'];

    /** @return HasMany<Thread, Board> */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /** @return HasMany<Post, Board> */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Return a valid Tailwind color token from DB color (fallback to 'sky'). */
    public function tailwindColor(): string
    {
        $raw = strtolower(trim((string) $this->color));

        if (preg_match('/^(?<base>[a-z]+)(?:-\d{2,3})?(?:\/\d{1,3})?$/', $raw, $m)) {
            $raw = $m['base'];
        }

        $aliases = [
            'grey' => 'stone',
            'gray' => 'slate',
            'aqua' => 'cyan',
            'turquoise' => 'teal',
            'navy' => 'blue',
        ];
        $raw = $aliases[$raw] ?? $raw;

        return in_array($raw, self::TAILWIND_ALLOWED, true) ? $raw : 'sky';
    }

    /** Convenience accessor: $board->color_token in Blade. */
    public function getColorTokenAttribute(): string
    {
        return $this->tailwindColor();
    }

    /** Helper: soft outlined button classes for this board color. */
    public function softButtonClasses(): string
    {
        $c = $this->tailwindColor();
        return "border border-{$c}-300 text-{$c}-600 bg-{$c}-50 hover:bg-{$c}-100 " .
               "dark:border-{$c}-700 dark:text-{$c}-300 dark:bg-{$c}-950/30 dark:hover:bg-{$c}-900/40";
    }

    protected static function booted(): void
    {
        static::saving(function (Board $board): void {
            if (empty($board->slug) && !empty($board->name)) {
                $board->slug = Str::slug($board->name);
            }
        });
    }

    /**
     * @param  Builder<Board> $query
     * @return Builder<Board>
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * @param  Builder<Board> $query
     * @return Builder<Board>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('name');
    }
}