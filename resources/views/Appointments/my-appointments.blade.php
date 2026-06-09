@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">My Bookings</h2>
        <p class="text-sm text-gray-500 mt-1">Manage and track all your appointments</p>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">

        <div class="flex space-x-8 border-b border-gray-200 w-full md:w-auto">
            <button onclick="switchTab('upcoming')" id="tab-upcoming"
                class="pb-2 text-sm font-bold text-blue-600 border-b-2 border-blue-600 transition-colors focus:outline-none">
                Upcoming Bookings
            </button>
            <button onclick="switchTab('past')" id="tab-past"
                class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors focus:outline-none">
                Past Bookings
            </button>
        </div>

        <form id="searchForm" method="GET" action="{{ route('my.appointments') }}" class="mb-6">
            <div class="flex flex-col sm:flex-row gap-4">

                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </span>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                        class="w-full py-2.5 pl-10 pr-4 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search purpose, date (e.g., 16 Jan), or day (e.g., Friday)..." autocomplete="off">
                </div>

                <div class="sm:w-48">
                    <select name="filter" onchange="document.getElementById('searchForm').submit()"
                        class="w-full p-2.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                        <option value="">All Time</option>
                        <option value="this_week" {{ request('filter') == 'this_week' ? 'selected' : '' }}>This Week
                        </option>
                        <option value="this_month" {{ request('filter') == 'this_month' ? 'selected' : '' }}>This Month
                        </option>
                    </select>
                </div>

                @if (request('search') || request('filter'))
                    <a href="{{ route('my.appointments') }}"
                        class="px-4 py-2.5 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors text-center">
                        Clear
                    </a>
                @endif

            </div>
        </form>
    </div>

    <div id="view-upcoming" class="space-y-4">
        @if ($upcoming->isEmpty())
            <div class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200 border-dashed">
                <p class="text-gray-500">No upcoming appointments found.</p>
                <a href="{{ route('appointments.create') }}"
                    class="text-blue-600 text-sm font-semibold hover:underline mt-2 inline-block">Book New Appointment</a>
            </div>
        @else
            @foreach ($upcoming as $appt)
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col md:flex-row items-start md:items-center justify-between hover:shadow-md transition duration-200">

                    <div class="flex items-center gap-4 mb-4 md:mb-0 w-full md:w-1/4">
                        <div class="bg-blue-50 text-blue-600 rounded-lg p-3 text-center min-w-[70px]">
                            <span
                                class="block text-xl font-bold">{{ \Carbon\Carbon::parse($appt->date)->format('d') }}</span>
                            <span
                                class="block text-xs uppercase font-semibold">{{ \Carbon\Carbon::parse($appt->date)->format('M') }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($appt->date)->format('l') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($appt->time)->format('h:i A') }}</p>
                        </div>
                    </div>

                    <div class="w-full md:w-2/4 mb-4 md:mb-0 px-0 md:px-4 border-l-0 md:border-l border-gray-100">
                        <h4 class="font-bold text-gray-800 text-sm mb-1">{{ $appt->purpose }}</h4>
                        <p class="text-xs text-gray-500 mb-1"><span class="font-semibold">IPS:</span> {{ $appt->ips }}
                        </p>
                        <p class="text-xs text-gray-500"><span class="font-semibold">Location:</span> {{ $appt->location }}
                        </p>
                    </div>

                    <div class="w-full md:w-1/4 flex flex-col md:flex-row justify-end items-center gap-3">
                        @if ($appt->status == 'pending')
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200">Pending</span>
                            <form method="POST" action="{{ route('appointments.cancel', $appt->id) }}"
                                onsubmit="return confirm('Are you sure you want to cancel?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-xs text-red-600 hover:text-red-800 font-semibold underline">Cancel</button>
                            </form>
                        @elseif($appt->status == 'approved')
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">Approved</span>
                            <a href="{{ route('appointments.reschedule', $appt->id) }}"
                                class="text-xs text-blue-600 hover:text-blue-800 font-bold underline decoration-blue-300 hover:decoration-blue-800 underline-offset-4 transition">
                                Reschedule
                            </a>
                        @elseif($appt->status == 'rejected')
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">Rejected</span>
                        @else
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">{{ ucfirst($appt->status) }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="view-past" class="space-y-4 hidden">
        @if ($past->isEmpty())
            <div class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200 border-dashed">
                <p class="text-gray-500">No past appointment history.</p>
            </div>
        @else
            @foreach ($past as $appt)
                <div
                    class="bg-gray-50 rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col md:flex-row items-start md:items-center justify-between opacity-75 hover:opacity-100 transition duration-200">
                    <div class="flex items-center gap-4 mb-4 md:mb-0 w-full md:w-1/4">
                        <div class="bg-gray-200 text-gray-600 rounded-lg p-3 text-center min-w-[70px]">
                            <span
                                class="block text-xl font-bold">{{ \Carbon\Carbon::parse($appt->date)->format('d') }}</span>
                            <span
                                class="block text-xs uppercase font-semibold">{{ \Carbon\Carbon::parse($appt->date)->format('M') }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($appt->date)->format('l') }}</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($appt->time)->format('h:i A') }}</p>
                        </div>
                    </div>
                    <div class="w-full md:w-2/4 mb-4 md:mb-0 px-0 md:px-4 border-l-0 md:border-l border-gray-100">
                        <h4 class="font-bold text-gray-800 text-sm mb-1">{{ $appt->purpose }}</h4>
                        <p class="text-xs text-gray-500 mb-1"><span class="font-semibold">IPS:</span> {{ $appt->ips }}
                        </p>
                        <p class="text-xs text-gray-500"><span class="font-semibold">Location:</span> {{ $appt->location }}
                        </p>
                    </div>
                    <div class="w-full md:w-1/4 flex justify-end items-center">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600 border border-gray-300">{{ ucfirst($appt->status) }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        function switchTab(tab) {
            document.getElementById('view-upcoming').classList.add('hidden');
            document.getElementById('view-past').classList.add('hidden');

            document.getElementById('tab-upcoming').classList.remove('text-blue-600', 'border-blue-600', 'font-bold');
            document.getElementById('tab-upcoming').classList.add('text-gray-500', 'font-medium');

            document.getElementById('tab-past').classList.remove('text-blue-600', 'border-blue-600', 'font-bold');
            document.getElementById('tab-past').classList.add('text-gray-500', 'font-medium');

            document.getElementById('view-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.add('text-blue-600', 'border-blue-600', 'font-bold');
            document.getElementById('tab-' + tab).classList.remove('text-gray-500');
        }

        // --- AUTOMATIC LIVE SEARCH LOGIC ---
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);

            // Wait 500ms after the user stops typing before auto-submitting
            debounceTimer = setTimeout(() => {
                searchForm.submit();
            }, 500);
        });

        // Keep the text cursor at the end of the text on auto-load
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.focus();
        searchInput.value = val;
    </script>
@endsection
