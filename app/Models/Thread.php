<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 * @property-read Board $board
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Reply> $replies
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 *
 * @mixin \Eloquent
 */
final class Thread extends Model
{
    use HasFactory;
    /** @var list<string> */
    protected $fillable = [
        'board_id',
        'user_id',
        'title',
        'slug',
        'body',
        'last_activity_at',
        'status',          // draft | scheduled | published
        'scheduled_for',   // datetime (UTC)
        'published_at',    // datetime (UTC)
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'scheduled_for'    => 'datetime',
            'published_at'     => 'datetime',
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

    /** ---------- Relations ---------- */

    /** @return BelongsTo<Board, Thread> */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }
    
    /** @return BelongsTo<User, Thread> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /** @return HasMany<Reply, Thread> */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }
    
    /** @return BelongsToMany<Tag, Thread> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /** ---------- Scopes ---------- */

    /**
     * Only visible threads.
     * @param  Builder<Thread> $query
     * @return Builder<Thread>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Threads that should appear publicly in lists.
     * @param  Builder<Thread> $query
     * @return Builder<Thread>
     */
    public function scopeVisibleForList(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<Thread> $query
     * @return Builder<Thread>
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
                     ->whereNotNull('scheduled_for')
                     ->where('scheduled_for', '>', now());
    }

    /**
     * @param  Builder<Thread> $query
     * @return Builder<Thread>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Alias used by some admin code.
     * @param  Builder<Thread> $query
     * @return Builder<Thread>
     */
    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /** ---------- Status helpers ---------- */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled'
            && $this->scheduled_for !== null
            && $this->scheduled_for->isFuture();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    /** ---------- Routing ---------- */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** ---------- Boot ---------- */
    protected static function booted(): void
    {
        static::saving(function (Thread $t): void {
            if (empty($t->slug) && !empty($t->title)) {
                $t->slug = Str::slug($t->title);
            }
            if (empty($t->last_activity_at)) {
                $t->last_activity_at = now();
            }
        });
    }
}