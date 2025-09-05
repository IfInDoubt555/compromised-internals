<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserProfileFactory>
 *
 * @property int                               $id
 * @property int                               $user_id
 * @property string|null                       $display_name
 * @property string|null                       $location
 * @property string|null                       $rally_role
 * @property int|null                          $rally_fan_since
 * @property \Illuminate\Support\Carbon|null   $birthday
 * @property string|null                       $bio
 * @property string|null                       $favorite_driver
 * @property string|null                       $favorite_car
 * @property string|null                       $favorite_event
 * @property string|null                       $favorite_game
 * @property string|null                       $car_setup_notes
 * @property string|null                       $website
 * @property string|null                       $instagram
 * @property string|null                       $youtube
 * @property string|null                       $twitter
 * @property string|null                       $twitch
 * @property string|null                       $profile_color
 * @property string|null                       $layout_style
 * @property string|null                       $banner_image
 * @property bool                              $show_birthday
 * @property bool                              $show_car_setup_notes
 * @property bool                              $show_favorites
 * @property bool                              $show_location
 * @property bool                              $show_socials
 */
class UserProfile extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserProfileFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'display_name',
        'location',
        'rally_role',
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
        'layout_style',
        'banner_image',
        'show_birthday',
        'show_car_setup_notes',
        'show_favorites',
        'show_location',
        'show_socials',
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'birthday'             => 'date',
            'rally_fan_since'      => 'integer',
            'show_birthday'        => 'boolean',
            'show_car_setup_notes' => 'boolean',
            'show_favorites'       => 'boolean',
            'show_location'        => 'boolean',
            'show_socials'         => 'boolean',
        ];
    }

    /** @return BelongsTo<User, UserProfile> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string|null> */
    public function socialLinks(): array
    {
        return [
            'website'   => $this->website,
            'instagram' => $this->instagram,
            'youtube'   => $this->youtube,
            'twitter'   => $this->twitter,
            'twitch'    => $this->twitch,
        ];
    }

    public function isBirthday(): bool
    {
        $bday = $this->birthday;
        if (!$bday instanceof Carbon) {
            return false;
        }

        $today = now();
        return (int) $bday->month === (int) $today->month
            && (int) $bday->day === (int) $today->day;
    }
}