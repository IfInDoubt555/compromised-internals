<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Models\UserProfile;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * These attributes are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'title',
        'banned_at',
        'profile_picture',
    ];

    /**
     * Hidden attributes for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts for attribute types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_admin'          => 'integer',
        'banned_at'         => 'datetime',
    ];

    /**
     * Always include these accessors on the model.
     */
    protected $appends = [
        'profile_picture_url',
    ];

    /**
     * Relationships
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user_likes')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Business Logic Helpers
     */
    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

    public function isBanned(): bool
    {
        return ! is_null($this->banned_at);
    }

    /**
     * Accessor: getProfilePictureUrlAttribute
     * 
     * Returns a full URL for the avatar, or a default if none set.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture && Storage::disk('public')->exists($this->profile_picture)) {
            return Storage::url($this->profile_picture);
        }

        return asset('images/default-avatar.png');
    }
}