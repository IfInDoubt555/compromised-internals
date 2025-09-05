<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    /** @var list<string> */
    protected $fillable = ['name', 'slug'];

    /**
     * Normalize slug on save; derive from name if empty.
     */
    protected static function booted(): void
    {
        static::saving(function (Tag $tag): void {
            $tag->slug = $tag->slug
                ? Str::slug($tag->slug)
                : Str::slug((string) $tag->name);
        });
    }

    /**
     * @return BelongsToMany<Thread, Tag>
     */
    public function threads(): BelongsToMany
    {
        // Ensure your pivot table name matches your schema (e.g., 'tag_thread')
        return $this->belongsToMany(Thread::class, 'tag_thread')->withTimestamps();
    }

    /**
     * @return BelongsToMany<Post, Tag>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag')->withTimestamps();
    }
}