<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'location',
        'rally_fan_since',
        'birthday',
        'bio',
        'favorite_driver',
        'favorite_car',
        'favorite_event',
        'favorite_game',
        'car_setup_notes',
        'website',
        'instagram',
        'youtube',
        'twitter',
        'twitch',
        'profile_color',
        'banner_image',
        'layout_style',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}