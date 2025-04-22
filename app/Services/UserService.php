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

        // Update base user info
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or create extended profile details
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
