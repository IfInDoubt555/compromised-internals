<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserFactory>
 *
 * @property int                                    $id
 * @property string                                 $name
 * @property string                                 $email
 * @property string|null                            $password
 * @property string|null                            $profile_picture
 * @property string|null                            $title
 * @property bool                                   $is_admin
 * @property \Illuminate\Support\Carbon|null        $email_verified_at
 * @property \Illuminate\Support\Carbon|null        $banned_at
 *
 * @property-read \App\Models\UserProfile|null                                       $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\Post>     $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\Order>    $orders
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\Comment>  $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int,\App\Models\Post>     $likedPosts
 *
 * @property-read string                          $profile_picture_url
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'title',
        'banned_at',
        'is_admin',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_admin'          => 'boolean',
        'banned_at'         => 'datetime',
    ];

    /** @var list<string> */
    protected $appends = [
        'profile_picture_url',
    ];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /** ---------- Relations ---------- */

        /** @return HasMany<App\Models\Post, App\Models\User> */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** @return HasMany<App\Models\Order, App\Models\User> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasOne<\App\Models\UserProfile, \App\Models\User> */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /** @return BelongsToMany<\App\Models\Post, \App\Models\User> */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    /** @return HasMany<\App\Models\Comment, \App\Models\User> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }
    
    /** @property-read string $profile_picture_url */
    public function getProfilePictureUrlAttribute(): string
    {
        $p = (string) ($this->profile_picture ?? '');

        if ($p === '') {
            return asset('images/default-avatar.png');
        }

        if (Str::startsWith($p, ['http://', 'https://', '//'])) {
            return $p;
        }

        return Storage::url($p);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('profile') && $this->profile) {
            return $this->profile->display_name ?: $this->name;
        }

        return optional($this->profile)->display_name ?: $this->name;
    }
}