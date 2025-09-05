<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\CarbonImmutable;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

/**
 * @property int $id
 * @property int|null $board_id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $body
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property 'draft'|'scheduled'|'published' $status
 * @property \Illuminate\Support\Carbon|null $scheduled_for
 * @property \Illuminate\Support\Carbon|null $published_at
 *
 * @property-read \App\Models\Board $board
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\Reply> $replies
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\Tag> $tags
 *
 * @mixin \Eloquent
 */

class Thread extends Model
{
    protected $fillable = [
        'board_id',
        'user_id',
        'title',
        'slug',
        'body',
        'last_activity_at',
        // scheduling
        'status',          // draft | scheduled | published
        'scheduled_for',   // datetime (UTC)
        'published_at',    // datetime (UTC)
    ];

       /** Thread belongs to one Board */
    public function board(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Board::class);
    }

    /** Only visible threads */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
                 ->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
    }


    protected function casts(): array
    {
        return [
            'last_activity_at' => 'immutable_datetime',
            'scheduled_for'    => 'immutable_datetime',
            'published_at'     => 'immutable_datetime',
        ];
    }

    public function getBodyHtmlAttribute(): string
    {
        static $converter = null;

        if ($converter === null) {
            $env = new Environment([
                'html_input'         => 'allow',
                'allow_unsafe_links' => false,
            ]);
            $env->addExtension(new CommonMarkCoreExtension());
            $converter = new MarkdownConverter($env);
        }

        return $converter->convert((string) $this->body)->getContent();
    }

    /** ---------- Scopes ---------- */

    /**
     * Threads that should appear publicly in lists.
     * Use this everywhere you count or list threads so results match.
     */
    public function scopeVisibleForList(Builder $q): Builder
    {
        return $q->where('status', 'published')
                 ->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
    }

    public function scopeScheduled(Builder $q): Builder
    {
        // ✅ scheduled_for (not published_at)
        return $q->where('status', 'scheduled')
                 ->whereNotNull('scheduled_for')
                 ->where('scheduled_for', '>', now());
    }
        /** Alias used by some admin code */
    public function scopeDraft(Builder $q): Builder
    {
        return $q->where('status', 'draft');
    }

    public function scopeDrafts(Builder $q): Builder
    {
        return $q->where('status', 'draft');
    }

    /** ---------- Status helpers ---------- */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        // ✅ scheduled_for (not published_at)
        return $this->status === 'scheduled'
            && $this->scheduled_for
            && $this->scheduled_for->isFuture();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at
            && $this->published_at->isPast();
    }

    /** ---------- Relations ---------- */
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function replies(): HasMany   { return $this->hasMany(Reply::class); }

    public function tags(): BelongsToMany
    {
        // ensure your pivot table name matches your schema (e.g., 'thread_tag')
        return $this->belongsToMany(Tag::class, 'thread_tag')->withTimestamps();
    }

    /** ---------- Routing ---------- */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** ---------- Boot ---------- */
    protected static function booted(): void
    {
        static::saving(function (Thread $t) {
            if (empty($t->slug) && !empty($t->title)) {
                $t->slug = Str::slug($t->title);
            }
            if (empty($t->last_activity_at)) {
                $t->last_activity_at = now();
            }
        });
    }
}