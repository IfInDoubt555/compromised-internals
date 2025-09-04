<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Normalize slug on save; derive from name if empty.
     */
    protected static function booted(): void
    {
        static::saving(function (Tag $tag) {
            $tag->slug = $tag->slug
                ? Str::slug($tag->slug)
                : Str::slug((string) $tag->name);
        });
    }

    public function threads(): BelongsToMany
    {
        // Use explicit pivot name and timestamps
        return $this->belongsToMany(Thread::class, 'tag_thread')->withTimestamps();
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag')->withTimestamps();
    }
}