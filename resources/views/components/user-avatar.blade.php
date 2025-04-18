@props(['size' => 'w-10 h-10'])

<img 
    src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-avatar.png') }}"
    alt="User Avatar"
    class="{{ $size }} rounded-full object-cover"
/>
