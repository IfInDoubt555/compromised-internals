<?php

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
 * @property int                       $id
 * @property string                    $name
 * @property string                    $email
 * @property string|null               $password
 * @property string|null               $profile_picture
 * @property string|null               $title
 * @property bool                      $is_admin
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property \Carbon\CarbonImmutable|null $banned_at
 *
 * @property-read UserProfile|null     $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int,Post>    $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int,Order>   $orders
 * @property-read \Illuminate\Database\Eloquent\Collection<int,Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int,Post>    $likedPosts
 *
 * @property-read string               $profile_picture_url
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    /** @var array<int,string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'title',
        'banned_at',
        'is_admin',
    ];

    /** @var array<int,string> */
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

    /** @var array<int,string> */
    protected $appends = [
        'profile_picture_url',
    ];

    /** Is the user an administrator? */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /** ---------- Relations ---------- */

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_user_likes')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** Is the user banned? */
    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    /**
     * Accessor: full URL for the profile picture.
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
     * Accessor: display_name (profile.display_name → name).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('profile') && $this->profile) {
            return $this->profile->display_name ?: $this->name;
        }

        return optional($this->profile)->display_name ?: $this->name;
    }
}