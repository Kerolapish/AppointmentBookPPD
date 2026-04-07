@extends('layouts.app')

@section('content')

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Requests</h1>
            <p class="text-sm text-gray-500 mt-1">View and manage all appointment applications.</p>
        </div>

        <div class="flex gap-3">
            <span class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-sm font-semibold shadow-sm text-gray-600">
                <span class="text-green-500 font-bold mr-1">{{ $approved->count() }}</span> Confirmed
            </span>
            <span class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-sm font-semibold shadow-sm text-gray-600">
                <span class="text-yellow-500 font-bold mr-1">{{ $pending->count() }}</span> Pending
            </span>
            <span class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-sm font-semibold shadow-sm text-gray-600">
                <span class="text-red-500 font-bold mr-1">{{ $rejected->count() }}</span> Rejected
            </span>
        </div>
    </div>

    <div class="space-y-10">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">

                    <form method="GET" action="{{ route('admin.requests') }}"
                        class="w-full flex flex-col md:flex-row gap-4">

                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Search History
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search user by name..."
                                    class="pl-10 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block p-2.5 text-sm border shadow-sm transition duration-150 ease-in-out">
                            </div>
                        </div>

                        <div class="w-full md:w-48">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Date Range
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <select name="filter"
                                    class="pl-10 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block p-2.5 text-sm border shadow-sm cursor-pointer">
                                    <option value="">All Time</option>
                                    <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today
                                    </option>
                                    <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>This Week
                                    </option>
                                    <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>This Month
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-md shadow-sm text-sm transition-colors duration-200 flex items-center gap-2">
                                <span>Filter</span>
                            </button>

                            @if (request()->has('search') || request()->has('filter'))
                                <a href="{{ route('admin.requests') }}"
                                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-4 rounded-md shadow-sm text-sm transition-colors duration-200">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                <h2 class="text-lg font-bold text-gray-900">Incoming Requests</h2>
            </div>

            @if ($pending->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-semibold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Applicant Name</th>
                                <th class="px-6 py-4">IPS / Purpose</th>
                                <th class="px-6 py-4">Requested Date</th>
                                <th class="px-6 py-4">Contact Info</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach ($pending as $apt)
                                <tr class="hover:bg-yellow-50/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $apt->user->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $apt->purpose }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($apt->date)->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($apt->time)->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900">{{ $apt->user->phone ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $apt->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.approve', $apt->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-green-50 text-green-700 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-green-100 transition border border-green-200">
                                                    Confirm
                                                </button>
                                            </form>

                                            <button type="button"
                                                onclick="openRescheduleModal('{{ $apt->id }}', '{{ $apt->user->name ?? 'Unknown' }}')"
                                                class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-yellow-100 transition border border-yellow-200">
                                                Reschedule
                                            </button>

                                            <button type="button"
                                                onclick="openRejectModal(
                                                    '{{ $apt->id }}', 
                                                    '{{ $apt->user->name ?? 'Unknown' }}', 
                                                    '{{ $apt->ips }}', 
                                                    '{{ \Carbon\Carbon::parse($apt->date)->format('d M Y') }}', 
                                                    '{{ $apt->user->email }}'
                                                )"
                                                class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-red-100 transition border border-red-200">
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-10 text-center flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                    <i class="fa-regular fa-folder-open text-4xl mb-3 opacity-20"></i>
                    <p class="text-sm font-medium">No pending requests available.</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <h2 class="text-lg font-bold text-gray-900">Confirmed Appointments</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">IPS</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Phone Number</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($approved as $apt)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $apt->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $apt->purpose }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($apt->date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $apt->user->phone ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $apt->user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Confirmed
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic bg-gray-50">No
                                    confirmed appointments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <h2 class="text-lg font-bold text-gray-900">Rejected Appointments</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">IPS</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Phone Number</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4 text-right">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($rejected as $apt)
                            <tr class="hover:bg-gray-50 transition opacity-80">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $apt->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $apt->purpose }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($apt->date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $apt->user->phone ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $apt->user->email }}</td>
                                <td class="px-6 py-4 text-right font-medium text-red-600 uppercase text-xs tracking-wide">
                                    {{ $apt->reject_reason ?? 'Slot Unavailable' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic bg-gray-50">No
                                    rejected appointments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeRejectModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                        REJECT CONFIRMATION
                    </h3>
                </div>

                <form id="rejectForm" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 space-y-4">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Name</label>
                            <input type="text" id="modal_name"
                                class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-500 sm:text-sm cursor-not-allowed"
                                readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">IPS</label>
                            <input type="text" id="modal_ips"
                                class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-500 sm:text-sm cursor-not-allowed"
                                readonly>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Date</label>
                                <input type="text" id="modal_date"
                                    class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-500 sm:text-sm cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                <input type="text" id="modal_email"
                                    class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 text-gray-500 sm:text-sm cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <div>
                            <label for="reject_reason" class="block text-sm font-bold text-gray-900 mb-1">Reason for
                                Rejection</label>
                            <select id="reject_reason" name="reason" onchange="toggleOtherField()"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md border">
                                <option value="" disabled selected>Select a reason...</option>
                                <option value="Clash date">Clash date</option>
                                <option value="Incomplete document">Incomplete document</option>
                                <option value="Slot unavailable">Slot unavailable</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div id="other_reason_container" class="hidden">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Please specify reason</label>
                            <textarea name="other_reason" rows="2"
                                class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2"
                                placeholder="Type the specific reason here..."></textarea>
                        </div>

                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            CONFIRM REJECT
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="rescheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeRescheduleModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
                    <h3 class="text-lg leading-6 font-bold text-yellow-900" id="modal-title">
                        REQUEST RESCHEDULE
                    </h3>
                </div>

                <form id="rescheduleForm" method="POST" action="">
                    @csrf

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 space-y-4">
                        <p class="text-sm text-gray-600 mb-2">
                            Ask <strong id="reschedule_modal_name" class="text-gray-900"></strong> to pick a new date and
                            time for this appointment.
                        </p>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Reason / Instructions for
                                User</label>
                            <textarea name="reason" rows="3" required
                                class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2"
                                placeholder="e.g., I am in a meeting at this time, please pick a slot after 2 PM"></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-500 text-base font-medium text-white hover:bg-yellow-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            SEND REQUEST
                        </button>
                        <button type="button" onclick="closeRescheduleModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openRejectModal(id, name, ips, date, email) {
            document.getElementById('modal_name').value = name;
            document.getElementById('modal_ips').value = ips;
            document.getElementById('modal_date').value = date;
            document.getElementById('modal_email').value = email;

            let form = document.getElementById('rejectForm');

            // FIXED: Use the route name. We replace the placeholder :id with the real ID.
            let url = "{{ route('admin.appointment.reject', ':id') }}";
            form.action = url.replace(':id', id);

            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectForm').reset();
            document.getElementById('other_reason_container').classList.add('hidden');
        }

        function toggleOtherField() {
            const select = document.getElementById('reject_reason');
            const otherContainer = document.getElementById('other_reason_container');
            const textarea = otherContainer.querySelector('textarea');

            if (select.value === 'Other') {
                otherContainer.classList.remove('hidden');
                textarea.required = true;
            } else {
                otherContainer.classList.add('hidden');
                textarea.required = false;
            }
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            // Reset form
            document.getElementById('rejectForm').reset();
            document.getElementById('other_reason_container').classList.add('hidden');
        }

        function toggleOtherField() {
            const select = document.getElementById('reject_reason');
            const otherContainer = document.getElementById('other_reason_container');

            if (select.value === 'Other') {
                otherContainer.classList.remove('hidden');
                otherContainer.querySelector('textarea').required = true;
            } else {
                otherContainer.classList.add('hidden');
                otherContainer.querySelector('textarea').required = false;
            }
        }

        function openRescheduleModal(id, name) {
            document.getElementById('reschedule_modal_name').innerText = name;

            let form = document.getElementById('rescheduleForm');

            // FIXED: Use the route name.
            let url = "{{ route('admin.appointment.reschedule', ':id') }}";
            form.action = url.replace(':id', id);

            document.getElementById('rescheduleModal').classList.remove('hidden');
        }

        function closeRescheduleModal() {
            // Hide the modal and reset the textarea
            document.getElementById('rescheduleModal').classList.add('hidden');
            document.getElementById('rescheduleForm').reset();
        }
    </script>

@endsection
