<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Board extends Model
{
    protected $fillable = ['name','slug','icon','color','position','is_public','description'];

    protected $casts = [
        'is_public' => 'boolean',
        'position'  => 'integer',
    ];

    // Optional: lets boards.index show counts without extra queries; remove if you prefer
    protected $withCount = ['threads'];

    /** Tailwind palette whitelist used for theming */
    public const TAILWIND_ALLOWED = [
        'slate','stone','red','orange','amber','yellow','lime','green',
        'emerald','teal','cyan','sky','blue','indigo','violet','purple',
        'fuchsia','pink','rose',
    ];

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    // Posts already reference board_id on Post
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Use slug in URLs (so Board $board binds by slug)
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Return a valid Tailwind color token from DB color (fallback to 'sky'). */
    public function tailwindColor(): string
    {
        $c = strtolower((string) $this->color);
        return in_array($c, self::TAILWIND_ALLOWED, true) ? $c : 'sky';
    }

    /** Convenience accessor: $board->color_token in Blade. */
    public function getColorTokenAttribute(): string
    {
        return $this->tailwindColor();
    }

    /** Optional helper: soft outlined button classes for this board color. */
    public function softButtonClasses(): string
    {
        $c = $this->tailwindColor();
        return "border border-{$c}-300 text-{$c}-600 bg-{$c}-50 hover:bg-{$c}-100 ".
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