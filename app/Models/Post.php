<?php

namespace App\Models;

use App\Models\Board;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Mews\Purifier\Facades\Purifier;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'excerpt',
        'slug',
        'body',
        'image_path',
        'user_id',
        'board_id',
        // scheduling
        'status',         // draft | scheduled | published | approved (legacy)
        'scheduled_for',  // datetime (UTC) - legacy helper
        'published_at',   // datetime (UTC)
        // 'publish_status', // legacy BC; still read in helpers
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
            'published_at'  => 'datetime',
        ];
    }

    /** ---------- Relations ---------- */
    public function user() { return $this->belongsTo(User::class); }
    public function board() { return $this->belongsTo(Board::class); }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_user_likes')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class)->latest();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag')->withTimestamps();
    }

    /** ---------- Accessors ---------- */
    public function getBodyHtmlAttribute(): string
    {
        /** @var MarkdownConverter|null $converter */
        static $converter = null;

        if ($converter === null) {
            $config = [
                'html_input'         => 'allow',  // allow safe inline HTML in Markdown source
                'allow_unsafe_links' => false,
            ];

            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension());

            $converter = new MarkdownConverter($environment);
        }

        $html = $converter->convert((string) $this->body)->getContent();

        // Sanitize to match public rendering (adjust profile if you have a custom one)
        return Purifier::clean($html, 'default');
    }

    public function getImageUrlAttribute(): string
    {
        $p = (string) $this->image_path;

        if ($p !== '' && Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }
        if ($p !== '') {
            return Storage::url($p); // e.g. /storage/xyz.jpg
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
        return Str::limit(Str::of($raw)->squish(), 160);
    }

    public function getMetaDescriptionAttribute(): string
    {
        return $this->excerpt_for_display;
    }

    /** ---------- Status helpers ---------- */
    public function isDraft(): bool
    {
        return ($this->status === 'draft')
            || (is_null($this->status) && ($this->publish_status ?? null) === 'draft'); // legacy
    }

    public function isScheduled(): bool
    {
        // New flow uses published_at as the schedule time
        return ($this->status === 'scheduled') && $this->published_at && $this->published_at->isFuture();
    }

    public function isPublished(): bool
    {
        // New flow
        if ($this->status === 'published' && $this->published_at && $this->published_at->isPast()) {
            return true;
        }
        // Legacy fallbacks
        if (is_null($this->status) && ($this->publish_status ?? null) === 'published') return true;
        if ($this->status === 'approved') return true;
        return false;
    }

    /** ---------- Scopes ---------- */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where(function ($q) {
            // New flow: published + published_at <= now
            $q->where(function ($q) {
                $q->where('status', 'published')
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', now());
            })
            // Legacy flow: publish_status column
            ->orWhere(function ($q) {
                $q->whereNull('status')->where('publish_status', 'published');
            })
            // Legacy: approved status
            ->orWhere('status', 'approved');
        });
    }

    public function scopeHot(Builder $q, int $days = 14): Builder
    {
        return $q->withCount(['likes', 'comments'])
            ->when($days > 0, fn ($qq) => $qq->where('created_at', '>=', now()->subDays($days)))
            ->selectRaw('(COALESCE(likes_count,0)*3 + COALESCE(comments_count,0)*2) as hot_score')
            ->orderByDesc('hot_score')
            ->orderByDesc('created_at');
    }

    public function scopeDrafts(Builder $q): Builder
    {
        return $q->where(function ($q) {
            $q->where('status', 'draft')
              ->orWhere(function ($q) {
                  $q->whereNull('status')->where('publish_status', 'draft'); // legacy
              });
        });
    }

    public function scopeScheduled(Builder $q): Builder
    {
        // New flow schedules via 'scheduled' status; UI sorts by published_at
        return $q->where('status', 'scheduled');
    }

    /** ---------- Routing ---------- */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** ---------- Model events ---------- */
    protected static function booted(): void
    {
        static::saving(function (self $post): void {
            if (empty($post->slug) && !empty($post->title)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->excerpt) && !empty($post->body)) {
                $post->excerpt = Str::limit(strip_tags($post->body), 100);
            }
        });
    }

    /** ---------- Feed ordering ---------- */
    public function scopeOrderForFeed(Builder $q): Builder
    {
        return $q->orderByRaw('COALESCE(published_at, created_at) DESC');
    }

    /** ---------- Convenience ---------- */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
