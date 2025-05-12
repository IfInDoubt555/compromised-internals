@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Manage Users</h1>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by name or email"
            class="px-4 py-2 border rounded shadow-sm w-full max-w-sm">
    </form>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-300 text-left">
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Role</th>
                <th class="p-3">Registered</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr class="border-b bg-gray-300 ">
                <td class="p-3">{{ $user->name }}</td>
                <td class="p-3">{{ $user->email }}</td>
                <td class="p-3">{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                <td class="p-3">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="p-3 text-sm space-x-2 whitespace-nowrap">
                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:underline">View</a>

                    @if (!$user->isBanned())
                    <form method="POST" action="{{ route('admin.users.ban', $user) }}" onsubmit="return confirm('Are you sure?')" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline">Ban</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline">Unban</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection