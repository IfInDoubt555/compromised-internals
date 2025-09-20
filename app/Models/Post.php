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

    // Return a URL to a specific variant, e.g. -640.webp
    public function variantUrl(int $w, ?string $format = null): ?string
    {
        $path = (string) $this->image_path;
        if ($path === '') return null;
    
        $dot = strrpos($path, '.');
        if ($dot === false) return null;
    
        $base = substr($path, 0, $dot);
        $ext  = strtolower($format ?: substr($path, $dot + 1));
    
        $variant = "{$base}-{$w}.{$ext}";
        return Storage::url($variant);
        }
    
    /**
     * Build a srcset like "…-480.webp 480w, …-768.webp 768w, …"
     * Only includes variants that exist.
     */
    public function srcset(array $widths, ?string $format = null): string
    {
        $out = [];
        foreach ($widths as $w) {
            $url = $this->variantUrl($w, $format);
            // If you want to skip non-existent files, uncomment the exists() check:
            // if (!\Storage::exists(str_replace('/storage/', '', $url))) continue;
            if ($url) $out[] = "{$url} {$w}w";
        }
        return implode(', ', $out);
    }

    public function heroSrcset(): ?string
    {
        $url = $this->image_url ?? null;
        if (!$url) return null;

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        if (!Str::startsWith($path, ['/storage/', 'storage/'])) return null; // external image → no srcset

        $rel = ltrim(Str::after($path, '/storage/'), '/');      // path relative to 'public' disk
        $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        if (!$ext) return null;

        $base = substr($rel, 0, -(strlen($ext) + 1));           // posts/foo/bar
        $widths = [640, 960, 1280, 1600, 1920];

        $build = function (string $ext) use ($base, $widths) {
            $out = [];
            foreach ($widths as $w) {
                $candidate = "{$base}-{$w}.{$ext}";
                if (Storage::disk('public')->exists($candidate)) {
                    $out[] = Storage::url($candidate) . " {$w}w";
                }
            }
            return $out ? implode(', ', $out) : null;
        };

        // Prefer modern formats; the <img> tag will still get the original ext via src/srcset.
        // (You’ll reference AVIF/WEBP in <source> tags from Blade if you want.)
        return $build($ext);
    }

    public function heroSizes(): string
    {
        // max-w-5xl (~1024px) container; 100vw on small
        return '(min-width:1280px) 1024px, (min-width:1024px) 1024px, 100vw';
    }

    // Optional: for cards
    public function cardSrcset(string $variant = 'default'): ?string
    {
        $thumb = $this->thumbnail_url ?? null;
        if (!$thumb) return null;

        $path = parse_url($thumb, PHP_URL_PATH) ?? '';
        if (!Str::startsWith($path, ['/storage/', 'storage/'])) return null;

        $rel = ltrim(Str::after($path, '/storage/'), '/');
        $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        if (!$ext) return null;

        $base = substr($rel, 0, -(strlen($ext) + 1));
        $widths = [160, 320, 640, 960, 1280];

        $out = [];
        foreach ($widths as $w) {
            $p = "{$base}-{$w}.{$ext}";
            if (Storage::disk('public')->exists($p)) {
                $out[] = Storage::url($p) . " {$w}w";
            }
        }
        return $out ? implode(', ', $out) : null;
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
