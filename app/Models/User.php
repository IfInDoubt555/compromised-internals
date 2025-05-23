<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        'profile_picture',   // ← allow if you ever mass‐assign it
        'title',
        'banned_at',
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
        'is_admin'          => 'integer',
        'banned_at'         => 'datetime',
    ];

    /**
     * Append these accessors to the model's array form.
     */
    protected $appends = [
        'profile_picture_url',
    ];

    /**
     * Is the user an administrator?
     */
    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

    /**
     * A user has many posts.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * A user has many orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * A user has one profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Is the user banned?
     */
    public function isBanned(): bool
    {
        return ! is_null($this->banned_at);
    }

    /**
     * Liked posts pivot.
     */
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user_likes')->withTimestamps();
    }

    /**
     * A user has many comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Accessor: full URL for the profile picture.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        // if they’ve got one on disk, return the /storage URL, otherwise the default
        if (
            $this->profile_picture
            && Storage::disk('public')->exists($this->profile_picture)
        ) {
            return Storage::url($this->profile_picture);
        }

        return asset('images/default-avatar.png');
    }
}