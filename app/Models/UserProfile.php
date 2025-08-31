<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','display_name','location','rally_role','rally_fan_since','birthday','bio',
        'favorite_driver','favorite_car','favorite_event','favorite_game','car_setup_notes',
        'website','instagram','youtube','twitter','twitch',
        'profile_color','banner_image','layout_style',
        'show_location','show_birthday','show_socials','show_favorites','show_car_setup_notes',
    ];

    protected $casts = [
        'birthday' => 'date',
        'show_location' => 'bool',
        'show_birthday' => 'bool',
        'show_socials' => 'bool',
        'show_favorites' => 'bool',
        'show_car_setup_notes' => 'bool',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function isBirthday(): bool
    {
        return $this->birthday ? Carbon::parse($this->birthday)->isBirthday() : false;
    }

    /** Derived: current age (int|null) */
    public function getAgeAttribute(): ?int
    {
        return $this->birthday ? $this->birthday->age : null;
    }

    /** Derived: years as a rally fan (int|null) */
    public function getFanYearsAttribute(): ?int
    {
        if (!is_numeric($this->rally_fan_since)) return null;
        $y = (int) $this->rally_fan_since;
        if ($y < 1900 || $y > (int) now()->year) return null;
        return (int) now()->year - $y;
    }

    /** Pack non-empty socials */
    public function socialLinks(): array
    {
        $links = [
            'website'   => $this->website,
            'instagram' => $this->instagram,
            'youtube'   => $this->youtube,
            'twitter'   => $this->twitter,
            'twitch'    => $this->twitch,
        ];
        return array_filter($links, fn($v) => filled($v));
    }
}