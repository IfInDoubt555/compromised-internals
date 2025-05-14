<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;

class UserService
{
    public static function updateProfile($user, Request $request): void
    {
        // Handle profile picture
        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $processedPath = ImageService::processAndStore(
                $request->file('profile_picture'),
                'profile_pics',
                'avatar_',
                400,
                400
            );

            if (!$processedPath) {
                throw new \InvalidArgumentException('Invalid image uploaded.');
            }

            $user->profile_picture = $processedPath;
        }

        // Update base user info safely (only intended user fields)
        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Save extended profile fields
        $profileData = $request->only([
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
            'banner_image',
            'layout_style',
        ]);

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData + ['user_id' => $user->id]);
        }

        // Reload profile relationship to reflect latest data
        $user->load('profile');
    }
}