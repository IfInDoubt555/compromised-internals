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