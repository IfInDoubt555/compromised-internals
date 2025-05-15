<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NoBannedWords;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new NoBannedWords],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'rally_role' => [
                'nullable',
                Rule::in(['Team Manager', 'Team Owner', 'Fan', 'Marshal', 'Scrutineer', 'Announcer', 'Sim Racer', 'Logistics', 'Sponsor', 'Medical Staff', 'Driver', 'Co-Driver', 'Media', 'Spectator', 'Technician', 'Club Organizer', 'Mechanic', 'Engineer', 'Coordinator', 'Volunteer']),
            ],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],

            // Add profanity filtering here:
            'bio'             => ['nullable', 'string', new NoBannedWords],
            'display_name'    => ['required', 'string', new NoBannedWords],
            'location'        => ['nullable', 'string', new NoBannedWords],
            'favorite_driver' => ['nullable', 'string', new NoBannedWords],
            'favorite_car'    => ['nullable', 'string', new NoBannedWords],
        ];
    }
}
