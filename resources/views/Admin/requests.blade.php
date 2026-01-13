@extends('layouts.app')

@section('content')
    {{-- Wrap the entire content in an Alpine component to manage modal state --}}
    <div x-data="{
        showRejectModal: false,
        rejectUrl: '',
        selectedAppointmentId: null,
        modalName: '',
        modalIps: '',
        modalDate: '',
        modalEmail: ''
    }" class="space-y-8 relative">

        {{-- ================================================ --}}
        {{-- SECTION 1: PENDING REQUESTS --}}
        {{-- ================================================ --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                    <i class="fa-regular fa-clock text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Pending Requests</h2>
                    <p class="text-sm text-gray-500">Action required for these appointments.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($pendingRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-gray-600 text-xs uppercase font-bold tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-4">Name / IPS</th>
                                    <th class="px-6 py-4">Purpose</th>
                                    <th class="px-6 py-4">Date & Time</th>
                                    <th class="px-6 py-4">Contact</th>
                                    <th class="px-6 py-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($pendingRequests as $request)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        {{-- 1. Name & IPS --}}
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">{{ $request->user->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500 font-mono mt-0.5">IPS: {{ $request->ips }}
                                            </div>
                                            <div
                                                class="mt-2 inline-block px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-[10px] font-medium">
                                                Waited: {{ $request->created_at->diffForHumans() }}
                                            </div>
                                        </td>

                                        {{-- 2. Purpose --}}
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $request->purpose }}
                                            </span>
                                        </td>

                                        {{-- 3. Date & Time --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2 text-gray-900 font-medium">
                                                <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                                {{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}
                                            </div>
                                            <div class="flex items-center gap-2 text-blue-600 text-sm mt-1 font-bold">
                                                <i class="fa-regular fa-clock text-xs"></i>
                                                {{ \Carbon\Carbon::parse($request->time)->format('h:i A') }}
                                            </div>
                                        </td>

                                        {{-- 4. Contact --}}
                                        <td class="px-6 py-4 text-sm">
                                            <div class="text-gray-900">
                                                {{ $request->phone ?? ($request->user->phone ?? '-') }}
                                            </div>
                                            <div class="text-gray-500 text-xs mt-0.5">{{ $request->user->email ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- 5. Actions --}}
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center items-center gap-2">
                                                <form action="{{ route('admin.approve', $request->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition transform hover:-translate-y-0.5">
                                                        APPROVE
                                                    </button>
                                                </form>

                                                <button type="button"
                                                    @click="
                                                        selectedAppointmentId = {{ $request->id }};
                                                        rejectUrl = '{{ route('admin.reject', $request->id) }}';
                                                        modalName = '{{ addslashes($request->user->name ?? 'Unknown') }}';
                                                        modalIps = '{{ $request->ips }}';
                                                        modalDate = '{{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}';
                                                        modalEmail = '{{ addslashes($request->user->email ?? '-') }}';
                                                        showRejectModal = true;
                                                    "
                                                    class="bg-white border border-red-200 text-red-500 hover:bg-red-50 text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition">
                                                    REJECT
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <i class="fa-regular fa-folder-open text-4xl text-gray-300 mb-3"></i>
                        <p>No pending requests at the moment.</p>
                    </div>
                @endif
            </div>
        </div>


        {{-- ================================================ --}}
        {{-- SECTION 2: APPROVED APPOINTMENTS --}}
        {{-- ================================================ --}}
        <div class="pt-4">
            <div class="flex justify-between items-end mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg text-green-600">
                        <i class="fa-regular fa-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Approved Appointments</h2>
                        <p class="text-sm text-gray-500">Scheduled upcoming appointments.</p>
                    </div>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Search approved..."
                        class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400 text-xs"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($approvedRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-gray-600 text-xs uppercase font-bold tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-4">Date & Time</th>
                                    <th class="px-6 py-4">Name / IPS</th>
                                    <th class="px-6 py-4">Purpose</th>
                                    <th class="px-6 py-4">Contact Info</th>
                                    <th class="px-6 py-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($approvedRequests as $request)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-green-700">
                                                {{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}
                                            </div>
                                            <div class="text-xs text-green-600 font-semibold mt-0.5">
                                                {{ \Carbon\Carbon::parse($request->time)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $request->user->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500">IPS: {{ $request->ips }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $request->purpose }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center gap-2 text-gray-700">
                                                <i class="fa-solid fa-phone text-gray-400 text-xs"></i>
                                                {{ $request->phone ?? ($request->user->phone ?? '-') }}
                                            </div>
                                            <div class="flex items-center gap-2 text-gray-500 text-xs mt-1">
                                                <i class="fa-solid fa-envelope text-gray-400 text-xs"></i>
                                                {{ $request->user->email ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button
                                                class="text-gray-500 hover:text-blue-600 border border-gray-200 hover:border-blue-300 bg-white px-3 py-1.5 rounded text-xs font-medium transition">
                                                Reschedule
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <p>No approved appointments found.</p>
                    </div>
                @endif
            </div>
        </div>


        {{-- ================================================ --}}
        {{-- SECTION 3: REJECTED HISTORY --}}
        {{-- ================================================ --}}
        <div class="pt-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-red-100 rounded-lg text-red-600">
                    <i class="fa-solid fa-ban text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Rejected History</h2>
                    <p class="text-sm text-gray-500">Previously rejected requests.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 text-gray-600 text-xs uppercase font-bold tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4">Name / IPS</th>
                                <th class="px-6 py-4">Date Requested</th>
                                <th class="px-6 py-4">Purpose</th>
                                <th class="px-6 py-4">Reason for Rejection</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($rejectedRequests as $request)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $request->user->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $request->ips }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $request->purpose }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded bg-red-50 text-red-700 text-xs font-medium border border-red-100">
                                            {{ $request->reason ?? 'No reason provided' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($rejectedRequests->count() == 0)
                    <div class="p-8 text-center text-gray-500">
                        <p>No rejected history found.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ================================================ --}}
        {{-- REJECT CONFIRMATION MODAL --}}
        {{-- ================================================ --}}
        <div x-show="showRejectModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/50 transition-opacity" @click="showRejectModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div x-show="showRejectModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-200"
                    @click.stop>
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-bold leading-6 text-gray-900" id="modal-title">
                            REJECT CONFIRMATION
                        </h3>
                        <button @click="showRejectModal = false" class="text-gray-400 hover:text-gray-500">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="bg-white px-6 py-6 space-y-4">
                        {{-- Read-only fields --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Name</label>
                            <input type="text" x-model="modalName" disabled
                                class="block w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm sm:text-sm px-3 py-2 cursor-not-allowed text-gray-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">IPS</label>
                            <input type="text" x-model="modalIps" disabled
                                class="block w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm sm:text-sm px-3 py-2 cursor-not-allowed text-gray-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Date</label>
                            <input type="text" x-model="modalDate" disabled
                                class="block w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm sm:text-sm px-3 py-2 cursor-not-allowed text-gray-600">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                            <input type="text" x-model="modalEmail" disabled
                                class="block w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm sm:text-sm px-3 py-2 cursor-not-allowed text-gray-600">
                        </div>

                        {{-- Form with Dynamic Action --}}
                        <form x-bind:action="rejectUrl" method="POST" class="mt-6">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="reason"
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Reason</label>
                                <select id="reason" name="reason"
                                    class="mt-1 block w-full rounded-lg border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-red-500 focus:outline-none focus:ring-red-500 bg-white shadow-sm"
                                    required>
                                    <option value="Clash date">Clash date</option>
                                    <option value="Incomplete document">Incomplete document</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-lg bg-red-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-600 transition sm:w-auto uppercase tracking-wider">
                                    CONFIRM
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
