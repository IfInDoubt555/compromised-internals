@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Manage Users</h1>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by name or email"
            class="ci-input w-full max-w-sm"
        >
    </form>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-xl shadow ring-1 ring-black/5 dark:ring-white/10">
        <table class="w-full table-auto border-collapse text-sm">
            <thead class="bg-gray-100 dark:bg-zinc-800/70 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Role</th>
                    <th class="p-3 text-left">Registered</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 dark:text-gray-100">
                @foreach ($users as $user)
                <tr class="border-t border-gray-200 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/60">
                    <td class="p-3">{{ $user->name }}</td>
                    <td class="p-3 text-blue-600 dark:text-sky-400">{{ $user->email }}</td>
                    <td class="p-3">{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                    <td class="p-3">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="p-3 text-sm space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 dark:text-sky-400 hover:underline">View</a>

                        @if (!$user->isBanned())
                        <form method="POST" action="{{ route('admin.users.ban', $user) }}" onsubmit="return confirm('Are you sure?')" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Ban</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 dark:text-green-400 hover:underline">Unban</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection