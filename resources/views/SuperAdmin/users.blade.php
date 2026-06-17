@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">User Management</h2>
        <p class="text-sm text-gray-500 mt-1">View and manage all registered users in the system.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div
@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">User Management</h2>
        <p class="text-sm text-gray-500 mt-1">View and manage all registered users in the system.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div
            class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50">
            <div>
                <h3 class="text-lg font-bold text-gray-800">All Users</h3>
                <p class="text-sm text-gray-500">{{ $users->total() }} total users registered.</p>
            </div>

            <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-3">
                <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add New Admin
                </button>
                <form id="searchForm" action="{{ route('super_admin.users') }}" method="GET" class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search users..."
                        class="block w-full sm:w-72 pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors"
                        autocomplete="off">

                    @if (request('search'))
                        <a href="{{ route('super_admin.users') }}"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">Role</th>
                        <th class="px-6 py-4 font-semibold">Joined</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if ($user->role === 'super_admin')
                                    <span
                                        class="px-2.5 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Super
                                        Admin</span>
                                @elseif($user->role === 'admin')
                                    <span
                                        class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Admin</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}')"
                                    class="text-blue-500 hover:text-blue-700 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition text-sm font-medium border border-transparent hover:border-blue-100">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No users found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <div id="editModal"
        class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">

            <button onclick="closeEditModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

            <h3 class="text-xl font-bold text-gray-900 mb-4">Edit User</h3>

            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" id="editName" name="name"
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="editEmail" name="email"
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="editRole" name="role"
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Admin Modal --}}
    <div id="createModal"
        class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">

            <button onclick="closeCreateModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-600"></i> Add New Admin
            </h3>

            <form action="{{ route('super_admin.users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="admin">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Temporary Password</label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                        Create Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, email, role) {
            // Set the form action URL dynamically
            let url = "{{ route('super_admin.users.update', ':id') }}";
            url = url.replace(':id', id);
            document.getElementById('editForm').action = url;

            // Populate inputs
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;

            // Show modal
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // --- AUTOMATIC LIVE SEARCH LOGIC ---
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    searchForm.submit();
                }, 500);
            });

            // Keep cursor at end
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.focus();
            searchInput.value = val;
        }

        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }
    </script>
@endsection
