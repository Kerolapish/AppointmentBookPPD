@extends('layouts.app')

@section('content')
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Master Dashboard</h2>
        <p class="text-sm text-gray-500 mt-1">Welcome to the Super Admin control center.</p>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Analytics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div
                class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl flex-shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Users</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</h3>
            </div>
        </div>

        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div
                class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-xl flex-shrink-0">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">New Users Today</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $newUsersToday }}</h3>
            </div>
        </div>

        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div
                class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xl flex-shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Appointments</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $totalAppointments }}</h3>
            </div>
        </div>

        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div
                class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xl flex-shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Pending Requests</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $pendingAppointments }}</h3>
            </div>
        </div>
    </div>

    {{-- Appointments Table Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div
            class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/50">
            <div>
                <h3 class="text-lg font-bold text-gray-800">All Appointments</h3>
                <p class="text-sm text-gray-500">A complete view of every booking in the system.</p>
            </div>

            {{-- Search Bar --}}
            <div class="w-full sm:w-auto">
                <form id="searchForm" action="{{ route('super_admin.dashboard') }}" method="GET" class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                        placeholder="Search name or email..."
                        class="block w-full sm:w-72 pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors"
                        autocomplete="off">

                    @if (request('search'))
                        <a href="{{ route('super_admin.dashboard') }}"
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
                        <th class="px-6 py-4 font-semibold">Client Details</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Time</th>
                        <th class="px-6 py-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($appointments as $appointment)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $appointment->user->name ?? 'Unknown User' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $appointment->user->email ?? 'No email' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <i class="fa-regular fa-calendar text-gray-400"></i>
                                    {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <i class="fa-regular fa-clock text-gray-400"></i>
                                    {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="openRescheduleModal({{ json_encode($appointment) }})"
                                        class="inline-flex items-center justify-center w-24 px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit
                                    </button>

                                    <form action="{{ route('super_admin.appointments.destroy', $appointment->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to cancel this appointment?')"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-24 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            <i class="fa-solid fa-trash-can mr-1.5"></i> Cancel
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-regular fa-calendar-xmark text-5xl mb-3 text-gray-300"></i>
                                    <p class="text-base font-medium">No appointments found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appointments->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

    {{-- Reschedule Modal --}}
    <div id="rescheduleModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Reschedule Appointment</h3>
                <button onclick="closeRescheduleModal()"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <form id="rescheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>
                        <input type="date" name="date" id="modal_date" required
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Time</label>
                        <input type="time" name="time" id="modal_time" required
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="modal_status"
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeRescheduleModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Cancel</button>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        function openRescheduleModal(appointment) {
            const modal = document.getElementById('rescheduleModal');
            const form = document.getElementById('rescheduleForm');

            form.action = `/super-admin/appointments/${appointment.id}/reschedule`;

            document.getElementById('modal_date').value = appointment.date;
            document.getElementById('modal_time').value = appointment.time;
            document.getElementById('modal_status').value = appointment.status;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRescheduleModal() {
            const modal = document.getElementById('rescheduleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
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
    </script>
@endsection
