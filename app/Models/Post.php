<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Board;
use Illuminate\Support\Str;

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

    public function isPublished(): bool
    {
        return $this->status === 'published' && !is_null($this->published_at);
    }

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

    /** Full URL for image or fallback */
    public function getImageUrlAttribute()
    {
        if ($this->image_path && file_exists(public_path('storage/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }
        return asset('images/default-post.png');
    }
}