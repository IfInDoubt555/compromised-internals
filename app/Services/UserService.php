<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\ImageService;

class UserService
{
    /**
     * Update the given user's core data + profile (including optional avatar/banner).
     */
    public static function updateProfile(User $user, Request $request): void
    {
        // --- 1) Validate payload --- //
        $validator = Validator::make($request->all(), [
            // core
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255'],

            // images
            'profile_picture' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'banner_image'    => ['nullable','image','mimes:jpg,jpeg,png,webp','max:8192'],

            // profile text
            'display_name'    => ['nullable','string','max:100'],
            'location'        => ['nullable','string','max:100'],
            'rally_role'      => ['nullable','string','max:50'],

            // numeric year as strict 4 digits (we'll coerce/guard again below)
            'rally_fan_since' => ['nullable','digits:4','integer','min:1900','max:'.now()->year],

            // dates
            'birthday'        => ['nullable','date','before:tomorrow'],

            'bio'             => ['nullable','string','max:2000'],
            'favorite_driver' => ['nullable','string','max:120'],
            'favorite_car'    => ['nullable','string','max:120'],
            'favorite_event'  => ['nullable','string','max:120'],
            'favorite_game'   => ['nullable','string','max:120'],
            'car_setup_notes' => ['nullable','string','max:4000'],

            // socials / links
            'website'   => ['nullable','string','max:255'],
            'instagram' => ['nullable','string','max:255'],
            'youtube'   => ['nullable','string','max:255'],
            'twitter'   => ['nullable','string','max:255'],
            'twitch'    => ['nullable','string','max:255'],

            // theming
            'profile_color' => ['nullable','string','max:20'],
            'layout_style'  => ['nullable','in:card,wide'],

            // privacy toggles
            'show_location'        => ['sometimes','boolean'],
            'show_birthday'        => ['sometimes','boolean'],
            'show_socials'         => ['sometimes','boolean'],
            'show_favorites'       => ['sometimes','boolean'],
            'show_car_setup_notes' => ['sometimes','boolean'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        // --- 2) Images (avatar + banner) --- //
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $avatarPath = ImageService::processAndStore($file, 'profile_pics', 'avatar_', 400, 400);
            if (! $avatarPath) {
                throw new \RuntimeException('Failed to process new avatar.');
            }
            $user->profile_picture = $avatarPath;
        }

        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');

            if ($user->profile?->banner_image && Storage::disk('public')->exists($user->profile->banner_image)) {
                Storage::disk('public')->delete($user->profile->banner_image);
            }

            // Wide, lightweight banner
            $bannerPath = ImageService::processAndStore($file, 'profile_banners', 'banner_', 1600, 400);
            if (! $bannerPath) {
                throw new \RuntimeException('Failed to process profile banner.');
            }
            // stash to $data for profile fill below
            $data['banner_image'] = $bannerPath;
        }

        // --- 3) Update core user --- //
        $user->fill([
            'name'  => strip_tags($data['name']),
            'email' => $data['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null; // force re-verify if changed
        }

        $user->save();

        // --- 4) Normalize fields --- //
        $cleanYear = null;
        if (isset($data['rally_fan_since']) && is_numeric($data['rally_fan_since'])) {
            $y = (int) $data['rally_fan_since'];
            if ($y >= 1900 && $y <= (int) now()->year) {
                $cleanYear = $y;
            }
        }

        $normalizeUrl = function (?string $v): ?string {
            if (!filled($v)) return null;
            $v = trim($v);
            if (str_starts_with($v, 'http://') || str_starts_with($v, 'https://')) return $v;
            // basic host-only -> https://host
            return 'https://' . ltrim($v, '/');
        };

        // --- 5) Persist profile (ensure record) --- //
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        $profile->fill([
            'display_name'    => strip_tags($data['display_name']    ?? $profile->display_name),
            'location'        => strip_tags($data['location']        ?? $profile->location),
            'rally_role'      => $data['rally_role']                 ?? $profile->rally_role,
            'rally_fan_since' => $cleanYear, // overwrite with normalized integer or null
            'birthday'        => $data['birthday']                   ?? $profile->birthday,
            'bio'             => strip_tags($data['bio']             ?? $profile->bio),

            'favorite_driver' => strip_tags($data['favorite_driver'] ?? $profile->favorite_driver),
            'favorite_car'    => strip_tags($data['favorite_car']    ?? $profile->favorite_car),
            'favorite_event'  => strip_tags($data['favorite_event']  ?? $profile->favorite_event),
            'favorite_game'   => strip_tags($data['favorite_game']   ?? $profile->favorite_game),
            'car_setup_notes' => $data['car_setup_notes']            ?? $profile->car_setup_notes,

            'website'   => $normalizeUrl($data['website']   ?? $profile->website),
            'instagram' => $normalizeUrl($data['instagram'] ?? $profile->instagram),
            'youtube'   => $normalizeUrl($data['youtube']   ?? $profile->youtube),
            'twitter'   => $normalizeUrl($data['twitter']   ?? $profile->twitter),
            'twitch'    => $normalizeUrl($data['twitch']    ?? $profile->twitch),

            'profile_color' => $data['profile_color'] ?? $profile->profile_color,
            'layout_style'  => $data['layout_style']  ?? $profile->layout_style,
            'banner_image'  => $data['banner_image']  ?? $profile->banner_image,
        ]);

        // flags: checkboxes might be absent => false
        foreach (['show_location','show_birthday','show_socials','show_favorites','show_car_setup_notes'] as $flag) {
            $profile->{$flag} = (bool) $request->boolean($flag);
        }

        $profile->save();

        // reload
        $user->load('profile');
    }
}