@extends('layouts.app')

@section('content')
    {{-- HEADER & SEARCH --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage user accounts and view details.</p>
        </div>
        <form method="GET" action="{{ route('admin.users') }}" class="relative w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email..."
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full md:w-64 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm bg-white">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-400 text-xs"></i>
        </form>
    </div>

    {{-- TABLE 1: ADMINISTRATORS (View Only - No Actions) --}}
    <div class="mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
            <span class="bg-purple-100 p-1.5 rounded text-purple-600 text-xs">
                <i class="fa-solid fa-user-shield"></i>
            </span>
            Administrators
        </h2>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Contact Info</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($admins as $admin)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ Str::substr($admin->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $admin->name }}</div>
                                            <div class="text-xs text-gray-400">ID: AD{{ $admin->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-gray-600 mb-1">
                                        <i class="fa-regular fa-envelope text-gray-400 text-xs"></i> {{ $admin->email }}
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="fa-solid fa-phone text-gray-400 text-xs"></i> {{ $admin->phone ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">Admin</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ \Carbon\Carbon::parse($admin->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No administrators found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TABLE 2: USERS --}}
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
            <span class="bg-blue-100 p-1.5 rounded text-blue-600 text-xs">
                <i class="fa-solid fa-users"></i>
            </span>
            Registered Users
        </h2>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Contact Info</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ Str::substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-400">ID: US{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-gray-600 mb-1">
                                        <i class="fa-regular fa-envelope text-gray-400 text-xs"></i> {{ $user->email }}
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="fa-solid fa-phone text-gray-400 text-xs"></i> {{ $user->phone ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">User</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No registered users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($users, 'hasPages') && $users->hasPages())
            <div class="mt-6">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
