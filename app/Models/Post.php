<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

/**
 * @use HasFactory<PostFactory>
 *
 * @property-read string $body_html
 * @property-read string $image_url
 * @property-read string $thumbnail_url
 * @property-read string $excerpt_for_display
 * @property-read string $meta_description
 * @property int $id
 * @property int|null $user_id
 * @property int|null $board_id
 * @property string $title
 * @property string|null $excerpt
 * @property string $slug
 * @property string|null $body
 * @property string|null $image_path
 * @property 'draft'|'scheduled'|'published'|'approved'|null $status
 * @property string|null $publish_status  // legacy column
 * @property \Illuminate\Support\Carbon|null $scheduled_for
 * @property \Illuminate\Support\Carbon|null $published_at
 *
 * @property-read User $user
 * @property-read Board $board
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 *
 * @mixin \Eloquent
 */
class Post extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'excerpt',
        'slug',
        'body',
        'image_path',
        'user_id',
        'board_id',
        'status',          // draft | scheduled | published | approved (legacy)
        'scheduled_for',   // datetime (UTC)
        'published_at',    // datetime (UTC)
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            // Use mutable Carbon to match phpdoc and silence PHPStan class-not-found for CarbonImmutable
            'scheduled_for' => 'datetime',
            'published_at'  => 'datetime',
        ];
    }

    /** ---------- Relations ---------- */

    /** @return BelongsTo<User, Post> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Board, Post> */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /** @return BelongsToMany<User, Post> */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    /** @return HasMany<Comment, Post> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** @return BelongsToMany<Tag, Post> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /** ---------- Markdown ---------- */

    public function getBodyHtmlAttribute(): string
    {
        /** @var MarkdownConverter|null $converter */
        static $converter = null;

        if ($converter === null) {
            $config = [
                'html_input'         => 'allow',
                'allow_unsafe_links' => false,
            ];
            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension());
            $converter = new MarkdownConverter($environment);
        }

        return $converter->convert((string) $this->body)->getContent();
    }

    /** ---------- Status helpers ---------- */

    public function isDraft(): bool
    {
        return ($this->status === 'draft')
            || (is_null($this->status) && ($this->publish_status ?? null) === 'draft'); // legacy
    }

    public function isScheduled(): bool
    {
        return ($this->status === 'scheduled')
            && $this->published_at !== null
            && $this->published_at->isFuture();
    }

    public function isPublished(): bool
    {
        if ($this->status === 'published' && $this->published_at !== null && $this->published_at->isPast()) {
            return true;
        }
        if (is_null($this->status) && ($this->publish_status ?? null) === 'published') {
            return true;
        }
        if ($this->status === 'approved') {
            return true;
        }
        return false;
    }

    /** ---------- Scopes ---------- */

    /**
     * KEEP this robust one; controllers should call ->public()
     * @param  Builder<Post> $query
     * @return Builder<Post>
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->where(function (Builder $q): void {
                $q->where('status', 'published')
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', now());
            })
            ->orWhere(function (Builder $q): void {
                $q->whereNull('status')
                  ->where('publish_status', 'published');
            })
            ->orWhere('status', 'approved');
        });
    }

    /**
     * Alias for controllers ->published()
     * @param  Builder<Post> $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $this->scopePublic($query);
    }

    /** @return Builder<Post> */
    public function scopeHot(Builder $query, int $days = 14): Builder
    {
        return $query->withCount(['likes', 'comments'])
            ->when($days > 0, fn ($qq): Builder =>
                $qq->where('created_at', '>=', now()->subDays($days))
            )
            ->selectRaw('(COALESCE(likes_count,0)*3 + COALESCE(comments_count,0)*2) as hot_score')
            ->orderByDesc('hot_score')
            ->orderByDesc('created_at');
    }

    /**
     * @param  Builder<Post> $query
     * @return Builder<Post>
     */
    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->where('status', 'draft')
              ->orWhere(function (Builder $q): void {
                  $q->whereNull('status')->where('publish_status', 'draft'); // legacy
              });
        });
    }

    /**
     * @param  Builder<Post> $query
     * @return Builder<Post>
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * @param  Builder<Post> $query
     * @return Builder<Post>
     */
    public function scopeOrderForFeed(Builder $query): Builder
    {
        return $query->orderByRaw('COALESCE(published_at, created_at) DESC');
    }

    /** ---------- Routing ---------- */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** ---------- Accessors ---------- */

    public function getImageUrlAttribute(): string
    {
        $p = (string) $this->image_path;

        if ($p !== '' && Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }
        if ($p !== '') {
            return Storage::url($p);
        }
        return asset('images/default-post.png');
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->image_url;
    }

    public function getExcerptForDisplayAttribute(): string
    {
        $raw = $this->excerpt ?: strip_tags((string) $this->body);
        return Str::limit((string) Str::of($raw)->squish()->toString(), 160);
    }

    public function getMetaDescriptionAttribute(): string
    {
        return $this->excerpt_for_display;
    }

    /** ---------- Model events ---------- */

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            if (empty($post->slug) && !empty($post->title)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->excerpt) && !empty($post->body)) {
                $post->excerpt = Str::limit(strip_tags($post->body), 100);
            }
        });
    }

    /** ---------- Convenience ---------- */

    public function isLikedBy(?User $user): bool
    {
        if ($user === null) {
            return false;
        }
        return $this->likes()->where('user_id', $user->getKey())->exists();
    }
}