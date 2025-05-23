<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Services\ImageService;

class UserService
{
    /**
     * Update the given user's core data + profile (including an optional avatar).
     */
    public static function updateProfile(User $user, Request $request): void
    {
        Log::info('UserService::updateProfile called', ['user_id' => $user->id]);

        // 1) Validate incoming payload
        $validator = Validator::make($request->all(), [
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255'],

            'display_name'    => ['nullable', 'string', 'max:100'],
            'location'        => ['nullable', 'string', 'max:100'],
            'rally_role'      => ['nullable', 'string', 'max:50'],
            'rally_fan_since' => ['nullable', 'integer', 'min:1900', 'max:' . now()->year],
            'birthday'        => ['nullable', 'date', 'before:today'],
            'bio'             => ['nullable', 'string', 'max:1000'],
            'favorite_driver' => ['nullable', 'string', 'max:100'],
            'favorite_car'    => ['nullable', 'string', 'max:100'],
            // … add any other profile rules here
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        // 2) Handle avatar upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // delete old avatar if present
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
                throw new \RuntimeException('Failed to process new avatar.');
            }

            $user->profile_picture = $path;
        }

        // 3) Update core user fields
        $user->fill([
            'name'  => strip_tags($data['name']),
            'email' => $data['email'],
        ]);

        if ($user->isDirty('email')) {
            // reset verification if email changed
            $user->email_verified_at = null;
        }

        $user->save();

        // 4) Build & persist profile record
        $profileData = [
            'display_name'    => strip_tags($data['display_name']    ?? $user->profile->display_name ?? ''),
            'location'        => strip_tags($data['location']        ?? $user->profile->location     ?? ''),
            'rally_role'      => $data['rally_role']                ?? $user->profile->rally_role   ?? null,
            'rally_fan_since' => $data['rally_fan_since']           ?? $user->profile->rally_fan_since ?? null,
            'birthday'        => $data['birthday']                  ?? $user->profile->birthday      ?? null,
            'bio'             => strip_tags($data['bio']             ?? $user->profile->bio           ?? ''),
            'favorite_driver' => strip_tags($data['favorite_driver'] ?? $user->profile->favorite_driver ?? ''),
            'favorite_car'    => strip_tags($data['favorite_car']    ?? $user->profile->favorite_car    ?? ''),
            // … etc
        ];

        // update or create
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // always eager-load fresh profile
        $user->load('profile');
    }
}