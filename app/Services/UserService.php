<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public static function updateProfile($user, Request $request): void
    {
        // Handle profile image
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = $request->file('profile_picture')->store('profile_pics', 'public');
        }

        // Update base info
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or create profile details
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'display_name', 'location', 'rally_fan_since', 'birthday', 'bio',
                'favorite_driver', 'favorite_car', 'favorite_event', 'favorite_game', 'car_setup_notes',
                'website', 'instagram', 'youtube', 'twitter', 'twitch',
                'profile_color', 'banner_image', 'layout_style',
            ])
        );
    }
}
