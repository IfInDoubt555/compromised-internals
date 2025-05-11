@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Manage Users</h1>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Role</th>
                <th class="p-3">Registered</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr class="border-b">
                <td class="p-3">{{ $user->name }}</td>
                <td class="p-3">{{ $user->email }}</td>
                <td class="p-3">{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                <td class="p-3">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="p-3 text-sm space-x-2">
                    <a href="#" class="text-blue-600 hover:underline">View</a>
                    <a href="#" class="text-yellow-600 hover:underline">Edit</a>
                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection