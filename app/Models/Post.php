<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Board; // <-- add this
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
        'status',
        'board_id', // <-- add this
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function board() // <-- add this
    {
        return $this->belongsTo(Board::class);
    }

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

    // Use slug instead of ID in route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Auto-generate excerpt if not set
    protected static function booted()
    {
        static::saving(function ($post) {
            if (empty($post->excerpt) && !empty($post->body)) {
                $post->excerpt = Str::limit(strip_tags($post->body), 100);
            }
        });
    }

    // Returns the full image URL or fallback to default-post.png
    public function getImageUrlAttribute()
    {
        if ($this->image_path && file_exists(public_path('storage/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }

        return asset('images/default-post.png');
    }
}