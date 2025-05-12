<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UserProfile;


class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'title',
        'banned_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'integer',
        'banned_at' => 'datetime', 
    ];
    
    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

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
    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user_likes')->withTimestamps();
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}