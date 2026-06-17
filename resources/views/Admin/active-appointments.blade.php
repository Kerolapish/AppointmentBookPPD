@extends('layouts.app')

@section('content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Active Bookings</h1>
                <p class="text-sm text-gray-500 mt-1">Manage current ongoing schedules and close them when finished.</p>
            </div>

            <div class="w-full md:w-80 relative">
                <input type="text" id="liveSearchInput" placeholder="Type to search automatically..."
                    value="{{ request('search') }}"
                    class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm bg-white">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-700 border-collapse">
                    <thead
                        class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">User/Student</th>
                            <th class="px-6 py-4">Purpose</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4">Date & Time</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="appointmentsTableBody">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $appointment->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 min-w-[180px] max-w-xs truncate">
                                    {{ $appointment->purpose }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $appointment->location }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="block font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}
                                    </span>
                                    <span class="text-xs text-gray-500 block mt-0.5">
                                        {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.appointments.complete', $appointment->id) }}"
                                        method="POST" onsubmit="return confirm('Confirm closing this appointment?');"
                                        class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm transition-colors">
                                            Complete & Close
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">
                                    No active appointments found matching those parameters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($appointments->hasPages())
            <div class="mt-6">
                {{ $appointments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('liveSearchInput');
            let timer = null;

            // Automatically push text cursor focus to the end of your string on query reloads
            if (searchInput.value.length > 0) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(timer);

                // 400ms typing debounce delay window
                timer = setTimeout(() => {
                    const searchValue = searchInput.value;
                    const currentUrl = new URL(window.location.href);

                    if (searchValue.trim() !== "") {
                        currentUrl.searchParams.set('search', searchValue);
                        // Reset pagination index counter back to page 1 on fresh search execution
                        currentUrl.searchParams.delete('page');
                    } else {
                        currentUrl.searchParams.delete('search');
                    }

                    window.location.href = currentUrl.toString();
                }, 400);
            });
        });
    </script>
@endsection
