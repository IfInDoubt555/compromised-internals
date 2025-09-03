<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'title',
        'banned_at',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for arrays / JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_admin'          => 'boolean',
        'banned_at'         => 'datetime',
    ];

    /**
     * Append these accessors to the model's array form.
     */
    protected $appends = [
        'profile_picture_url',
    ];

    /** Is the user an administrator? */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /** Relations */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user_likes')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Is the user banned? */
    public function isBanned(): bool
    {
        return ! is_null($this->banned_at);
    }

    /**
     * Accessor: full URL for the profile picture.
     * - Accepts absolute URLs stored in DB
     * - Otherwise treats value as a 'public' disk path
     * - Falls back to default avatar
     */
    public function getProfilePictureUrlAttribute(): string
    {
        $p = (string) ($this->profile_picture ?? '');

        if ($p === '') {
            return asset('images/default-avatar.png');
        }

        if (Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }

        return Storage::url($p); // /storage/...
    }

        /**
     * Accessor: display_name
     * Prefer the profile's display_name; fallback to the account name.
     */
    public function getDisplayNameAttribute(): string
    {
        // If relation is loaded, use it without another query.
        if ($this->relationLoaded('profile') && $this->profile) {
            return $this->profile->display_name ?: $this->name;
        }
        // Lazy fallback (single query if not preloaded)
        return optional($this->profile)->display_name ?: $this->name;
    }
}