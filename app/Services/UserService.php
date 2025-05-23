<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\ImageService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class UserService
{
    public static function updateProfile($user, Request $request): void
    {
        Log::info('FILES BAG:', $request->allFiles());

        // 1) Validate all incoming fields (including files)
        $validator = Validator::make($request->all(), [
            // user table
            'profile_picture'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255'],

            // profile table
            'banner_image'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'display_name'      => ['nullable', 'string', 'max:100'],
            'location'          => ['nullable', 'string', 'max:100'],
            'rally_role'        => ['nullable', 'integer'],
            'rally_fan_since'   => ['nullable', 'integer', 'min:1900', 'max:' . now()->year],
            'birthday'          => ['nullable', 'date', 'before:today'],
            'bio'               => ['nullable', 'string', 'max:1000'],
            'favorite_driver'   => ['nullable', 'string', 'max:100'],
            'favorite_car'      => ['nullable', 'string', 'max:100'],
            'favorite_event'    => ['nullable', 'string', 'max:100'],
            'favorite_game'     => ['nullable', 'string', 'max:100'],
            'car_setup_notes'   => ['nullable', 'string', 'max:1000'],
            'website'           => ['nullable', 'url', 'max:255'],
            'instagram'         => ['nullable', 'url', 'max:255'],
            'youtube'           => ['nullable', 'url', 'max:255'],
            'twitter'           => ['nullable', 'url', 'max:255'],
            'twitch'            => ['nullable', 'url', 'max:255'],
            'profile_color'     => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'layout_style'      => ['nullable', Rule::in(['compact', 'classic', 'photo-heavy'])],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        //
        // 2) Handle profile picture upload
        //
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // delete previous avatar
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = ImageService::processAndStore(
                $file,
                'profile_pics',
                'avatar_',
                400,
                400
            );

            if (! $path) {
                throw new \InvalidArgumentException('Failed to process profile picture.');
            }

            $user->profile_picture = $path;
        }

        //
        // 3) Handle banner image upload
        //
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');

            // delete previous banner
            if ($user->profile?->banner_image && Storage::disk('public')->exists($user->profile->banner_image)) {
                Storage::disk('public')->delete($user->profile->banner_image);
            }

            $path = ImageService::processAndStore(
                $file,
                'banner_images',
                'banner_',
                1200,
                300
            );

            if (! $path) {
                throw new \InvalidArgumentException('Failed to process banner image.');
            }

            // merge into the profile payload
            $data['banner_image'] = $path;
        }

        //
        // 4) Update core user fields
        //
        $user->fill([
            'name'  => strip_tags($data['name']),
            'email' => $data['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        //
        // 5) Build profile data array
        //
        $profileData = [
            'display_name'    => strip_tags($data['display_name'] ?? ''),
            'location'        => strip_tags($data['location'] ?? ''),
            'rally_role'      => $data['rally_role'] ?? null,
            'rally_fan_since' => $data['rally_fan_since'] ?? null,
            'birthday'        => $data['birthday'] ?? null,
            'bio'             => strip_tags($data['bio'] ?? ''),
            'favorite_driver' => strip_tags($data['favorite_driver'] ?? ''),
            'favorite_car'    => strip_tags($data['favorite_car'] ?? ''),
            'favorite_event'  => strip_tags($data['favorite_event'] ?? ''),
            'favorite_game'   => strip_tags($data['favorite_game'] ?? ''),
            'car_setup_notes' => strip_tags($data['car_setup_notes'] ?? ''),
            'website'         => $data['website'] ?? null,
            'instagram'       => $data['instagram'] ?? null,
            'youtube'         => $data['youtube'] ?? null,
            'twitter'         => $data['twitter'] ?? null,
            'twitch'          => $data['twitch'] ?? null,
            'profile_color'   => $data['profile_color'] ?? null,
            'layout_style'    => $data['layout_style'] ?? null,
        ];

        // include banner_image key if we set it above
        if (isset($data['banner_image'])) {
            $profileData['banner_image'] = $data['banner_image'];
        }

        //
        // 6) Persist profile
        //
        if ($user->relationLoaded('profile') && $user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        //
        // 7) Refresh relations
        //
        $user->load('profile');
    }
}