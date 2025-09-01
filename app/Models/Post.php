<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Board;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        'status',          // draft | scheduled | published
        'scheduled_for',   // datetime (UTC)
        'published_at',    // datetime (UTC)
        /* 'publish_status', */  // legacy for BC
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
            'published_at'  => 'datetime',
        ];
    }

    /** Scopes */
    public function scopePublished($q) { return $q->where('status', 'published'); }
    public function scopeScheduled($q) { return $q->where('status', 'scheduled'); }
    public function scopeDraft($q)     { return $q->where('status', 'draft'); }

    /** Relations */
    public function user()  { return $this->belongsTo(User::class); }
    public function board() { return $this->belongsTo(Board::class); }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_user_likes')->withTimestamps();
    }

    public function isLikedBy(?User $user)
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class)->latest();
    }

    /** Route model binding by slug */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /** Auto slug/excerpt */
    protected static function booted()
    {
        static::saving(function ($post) {
            if (empty($post->slug) && !empty($post->title)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->excerpt) && !empty($post->body)) {
                $post->excerpt = Str::limit(strip_tags($post->body), 100);
            }
        });
    }

    /**
     * Robust image URL:
     * - If image_path is an absolute URL, return it.
     * - Else if set, return Storage::url(image_path) (works with 'public' disk + symlink).
     * - Else return default placeholder.
     */
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

    /** Alias so blades can use $post->thumbnail_url interchangeably */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->image_url;
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag')->withTimestamps();
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
}