<section>
    <header>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Update Profile Information</h2>
        <p class="text-sm text-gray-600">
            Update your account's profile information and email address.
        </p>
    </header>

    <!-- Verification Trigger Form -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Main Profile Update Form -->
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Name -->
        <x-input name="name" label="Name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />

        <!-- Email -->
        <x-input name="email" label="Email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    Your email address is unverified.

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Click here to re-send the verification email.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        A new verification link has been sent to your email address.
                    </p>
                @endif
            </div>
        @endif

        <!-- Profile Picture -->
        <div>
            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>

            <div class="flex items-center gap-4">
                <input 
                    id="profile_picture"
                    name="profile_picture"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    onchange="previewAvatar(event)"
                    class="file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition text-sm border        border-gray-300 rounded-xl w-full max-w-sm"
                />
                <script>
                function previewAvatar(event) {
                    const input = event.target;
                    const preview = document.getElementById('avatar-preview');
                
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }
                </script>

                <!-- Avatar Preview -->
                <img id="avatar-preview" 
                     src="{{ $user->profile_picture_url ?? '/images/default-avatar.png' }}" 
                     alt="Avatar Preview" 
                     class="w-29 h-29 rounded-full shadow border object-cover" 
                />
            </div>

            <p class="text-xs text-gray-500 mt-1">Max file size: 2MB. Formats: JPG, PNG, WebP</p>

            @error('profile_picture')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Extended Profile Details -->
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Profile Details</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input name="display_name" label="Display Name" value="{{ old('display_name', $user->profile->display_name ?? '') }}" />
            <x-input name="location" label="Location" value="{{ old('location', $user->profile->location ?? '') }}" />
            <x-input name="rally_fan_since" label="Rally Fan Since" value="{{ old('rally_fan_since', $user->profile->rally_fan_since ?? '') }}" />
            <x-input name="birthday" label="Birthday" type="date" value="{{ old('birthday', $user->profile->birthday ?? '') }}" />
            <x-input name="favorite_driver" label="Favorite Driver" value="{{ old('favorite_driver', $user->profile->favorite_driver ?? '') }}" />
            <x-input name="favorite_car" label="Favorite Car" value="{{ old('favorite_car', $user->profile->favorite_car ?? '') }}" />
        </div>

        <x-textarea name="bio" label="About Me" value="{{ old('bio', $user->profile->bio ?? '') }}" class="mt-4" />

        <div class="flex items-center gap-4 mt-6">
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl shadow hover:bg-blue-700 transition">
            💾 Save
        </button>
            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Saved.</p>
            @endif
        </div>
    </form>
</section>
