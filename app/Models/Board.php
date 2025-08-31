<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Board extends Model
{
    protected $fillable = ['name','slug','icon','color','position','is_public','description'];

    /** Tailwind palette whitelist used for theming */
    public const TAILWIND_ALLOWED = [
        'slate','stone','red','orange','amber','yellow','lime','green',
        'emerald','teal','cyan','sky','blue','indigo','violet','purple',
        'fuchsia','pink','rose',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'position'  => 'integer',
    ];

    // Optional: lets boards.index show counts without extra queries
    protected $withCount = ['threads'];

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

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
        // Trim, lowercase
        $raw = strtolower(trim((string) $this->color));

        // Strip accidental shade/opacity suffixes like "emerald-600" or "emerald-600/30"
        if (preg_match('/^(?<base>[a-z]+)(?:-\d{2,3})?(?:\/\d{1,3})?$/', $raw, $m)) {
            $raw = $m['base'];
        }

        // Map common human aliases to Tailwind palette names
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
        // Auto-fill slug if missing
        static::saving(function (Board $board) {
            if (empty($board->slug) && !empty($board->name)) {
                $board->slug = Str::slug($board->name);
            }
        });
    }

    // Handy scopes (optional)
    public function scopePublic($q)  { return $q->where('is_public', true); }
    public function scopeOrdered($q) { return $q->orderBy('position')->orderBy('name'); }
}