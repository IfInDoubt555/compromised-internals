<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Update the given user's core data + profile (including optional avatar/banner).
     *
     * @throws ValidationException
     */
    public static function updateProfile(User $user, Request $request): void
    {
        // --- 1) Validate payload --- //
        $validator = Validator::make($request->all(), [
            // core
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],

            // images
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],

            // profile text
            'display_name'    => ['nullable', 'string', 'max:100'],
            'location'        => ['nullable', 'string', 'max:100'],
            'rally_role'      => ['nullable', 'string', 'max:50'],

            // numeric year as strict 4 digits
            'rally_fan_since' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . now()->year],

            // dates
            'birthday'        => ['nullable', 'date', 'before:tomorrow'],

            'bio'             => ['nullable', 'string', 'max:2000'],
            'favorite_driver' => ['nullable', 'string', 'max:120'],
            'favorite_car'    => ['nullable', 'string', 'max:120'],
            'favorite_event'  => ['nullable', 'string', 'max:120'],
            'favorite_game'   => ['nullable', 'string', 'max:120'],
            'car_setup_notes' => ['nullable', 'string', 'max:4000'],

            // socials / links
            'website'   => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'youtube'   => ['nullable', 'string', 'max:255'],
            'twitter'   => ['nullable', 'string', 'max:255'],
            'twitch'    => ['nullable', 'string', 'max:255'],

            // theming
            'profile_color' => ['nullable', 'string', 'max:20'],
            'layout_style'  => ['nullable', 'in:card,wide'],

            // privacy toggles
            'show_location'        => ['sometimes', 'boolean'],
            'show_birthday'        => ['sometimes', 'boolean'],
            'show_socials'         => ['sometimes', 'boolean'],
            'show_favorites'       => ['sometimes', 'boolean'],
            'show_car_setup_notes' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        /** @var array<string, mixed> $data */
        $data = $validator->validated();

        // helper cleaners
        /** @param string|null $v @return string|null */
        $cleanStr = static function (?string $v): ?string {
            return $v !== null ? strip_tags($v) : null;
        };

        /** @param string|null $v @return string|null */
        $normalizeUrl = static function (?string $v): ?string {
            if ($v === null || trim($v) === '') {
                return null;
            }
            $v = trim($v);
            if (str_starts_with($v, 'http://') || str_starts_with($v, 'https://')) {
                return $v;
            }
            return 'https://' . ltrim($v, '/');
        };

        // --- 2) Images (avatar + banner) --- //
        if ($request->hasFile('profile_picture')) {
            /** @var UploadedFile $file */
            $file = $request->file('profile_picture');

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            /** @var string|null $avatarPath */
            $avatarPath = app(ImageService::class)->processAndStore($file, 'profile_pics', 'avatar_', 400, 400);
            if ($avatarPath === null) {
                throw new \RuntimeException('Failed to process new avatar.');
            }
            $user->profile_picture = $avatarPath;
        }

        if ($request->hasFile('banner_image')) {
            /** @var UploadedFile $file */
            $file = $request->file('banner_image');

            if ($user->profile?->banner_image && Storage::disk('public')->exists($user->profile->banner_image)) {
                Storage::disk('public')->delete($user->profile->banner_image);
            }

            /** @var string|null $bannerPath */
            $bannerPath = app(ImageService::class)->processAndStore($file, 'profile_banners', 'banner_', 1600, 400);
            if ($bannerPath === null) {
                throw new \RuntimeException('Failed to process profile banner.');
            }
            // stash to $data for profile fill below
            $data['banner_image'] = $bannerPath;
        }

        // --- 3) Update core user --- //
        $user->fill([
            'name'  => (string) $cleanStr((string) ($data['name'] ?? $user->name)),
            'email' => (string) ($data['email'] ?? $user->email),
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

        // --- 5) Persist profile (ensure record) --- //
        /** @var UserProfile $profile */
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        $profile->fill([
            'display_name'    => array_key_exists('display_name', $data)    ? $cleanStr($data['display_name'])    : $profile->display_name,
            'location'        => array_key_exists('location', $data)        ? $cleanStr($data['location'])        : $profile->location,
            'rally_role'      => array_key_exists('rally_role', $data)      ? (is_string($data['rally_role']) ? $data['rally_role'] : $profile->rally_role) : $profile->rally_role,
            'rally_fan_since' => $cleanYear, // overwrite with normalized integer or null
            'birthday'        => array_key_exists('birthday', $data)        ? ($data['birthday'] ?: null)        : $profile->birthday,
            'bio'             => array_key_exists('bio', $data)             ? $cleanStr($data['bio'])             : $profile->bio,

            'favorite_driver' => array_key_exists('favorite_driver', $data) ? $cleanStr($data['favorite_driver']) : $profile->favorite_driver,
            'favorite_car'    => array_key_exists('favorite_car', $data)    ? $cleanStr($data['favorite_car'])    : $profile->favorite_car,
            'favorite_event'  => array_key_exists('favorite_event', $data)  ? $cleanStr($data['favorite_event'])  : $profile->favorite_event,
            'favorite_game'   => array_key_exists('favorite_game', $data)   ? $cleanStr($data['favorite_game'])   : $profile->favorite_game,
            'car_setup_notes' => array_key_exists('car_setup_notes', $data) ? (is_string($data['car_setup_notes']) ? $data['car_setup_notes'] : $profile->car_setup_notes) : $profile->car_setup_notes,

            'website'   => array_key_exists('website', $data)   ? $normalizeUrl(is_string($data['website']) ? $data['website'] : null)     : $profile->website,
            'instagram' => array_key_exists('instagram', $data) ? $normalizeUrl(is_string($data['instagram']) ? $data['instagram'] : null) : $profile->instagram,
            'youtube'   => array_key_exists('youtube', $data)   ? $normalizeUrl(is_string($data['youtube']) ? $data['youtube'] : null)     : $profile->youtube,
            'twitter'   => array_key_exists('twitter', $data)   ? $normalizeUrl(is_string($data['twitter']) ? $data['twitter'] : null)     : $profile->twitter,
            'twitch'    => array_key_exists('twitch', $data)    ? $normalizeUrl(is_string($data['twitch']) ? $data['twitch'] : null)       : $profile->twitch,

            'profile_color' => array_key_exists('profile_color', $data) ? (is_string($data['profile_color']) ? $data['profile_color'] : $profile->profile_color) : $profile->profile_color,
            'layout_style'  => array_key_exists('layout_style', $data)  ? (is_string($data['layout_style'])  ? $data['layout_style']  : $profile->layout_style)  : $profile->layout_style,
            'banner_image'  => array_key_exists('banner_image', $data)  ? (is_string($data['banner_image'])  ? $data['banner_image']  : $profile->banner_image)  : $profile->banner_image,
        ]);

        // flags: checkboxes might be absent => false
        foreach (['show_location', 'show_birthday', 'show_socials', 'show_favorites', 'show_car_setup_notes'] as $flag) {
            $profile->{$flag} = (bool) $request->boolean($flag);
        }

        $profile->save();

        // reload
        $user->load('profile');
    }
}