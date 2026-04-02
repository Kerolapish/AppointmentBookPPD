@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your appointments with Pejabat Pendidikan Daerah Kluang</p>
        </div>

        <div class="space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                            <i class="fa-solid fa-calendar-check text-xl"></i>
                        </div>
                        {{-- Dynamic Badge --}}
                        <span
                            class="text-xs font-bold px-2 py-1 rounded-full {{ $percentageChange >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $percentageChange > 0 ? '+' : '' }}{{ $percentageChange }}%
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $totalAppointments }}</h3>
                        <p class="text-gray-500 text-sm">Total Appointments</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="p-3 bg-amber-50 rounded-lg text-amber-600">
                            <i class="fa-regular fa-clock text-xl"></i>
                        </div>
                        <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-1 rounded-full">Pending</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</h3>
                        <p class="text-gray-500 text-sm">Pending Approval</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="p-3 bg-green-50 rounded-lg text-green-600">
                            <i class="fa-regular fa-circle-check text-xl"></i>
                        </div>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">Active</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['confirmed'] }}</h3>
                        <p class="text-gray-500 text-sm">Confirmed</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                            <i class="fa-regular fa-calendar text-xl"></i>
                        </div>
                        <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2 py-1 rounded-full">This
                            Month</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['upcoming'] }}</h3>
                        <p class="text-gray-500 text-sm">Upcoming</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <div class="lg:col-span-3 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Upcoming Appointments</h2>
                        <a href="{{ route('my.appointments') }}"
                            class="text-blue-600 hover:text-blue-700 text-sm font-medium hover:underline flex items-center gap-1">
                            View All <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    @foreach ($upcomingAppointments as $appointment)
                        @php
                            $dateObj = \Carbon\Carbon::parse($appointment->date);
                            $timeObj = \Carbon\Carbon::parse($appointment->time);
                        @endphp

                        <div
                            class="mb-4 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition {{ $appointment->status === 'reschedule_requested' ? 'border-yellow-400 ring-1 ring-yellow-400' : '' }}">

                            <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="bg-blue-50 text-blue-600 px-4 py-3 rounded-xl text-center min-w-[70px] border border-blue-100">
                                        <span
                                            class="block text-xs font-bold uppercase tracking-wider">{{ $dateObj->format('M') }}</span>
                                        <span class="block text-xl font-bold">{{ $dateObj->format('d') }}</span>
                                    </div>

                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $appointment->purpose }}</h3>
                                        <p class="text-xs text-blue-500 font-semibold mb-1">{{ $appointment->ips }}</p>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                            <span><i class="fa-regular fa-clock mr-1"></i>
                                                {{ $timeObj->format('h:i A') }}</span>
                                            <span class="hidden sm:inline text-gray-300">|</span>
                                            <span><i class="fa-solid fa-location-dot mr-1"></i>
                                                {{ $appointment->location }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-shrink-0">
                                    @if ($appointment->status == 'confirmed')
                                        <span
                                            class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full">Confirmed</span>
                                    @elseif($appointment->status == 'pending')
                                        <span
                                            class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-full">Pending</span>
                                    @elseif($appointment->status == 'reschedule_requested')
                                        <span
                                            class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1.5 rounded-full">Action
                                            Required</span>
                                    @else
                                        <span
                                            class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1.5 rounded-full">{{ ucfirst($appointment->status) }}</span>
                                    @endif
                                </div>
                            </div>

                            @if ($appointment->status === 'reschedule_requested')
                                <div
                                    class="bg-yellow-50 border-t border-yellow-200 p-4 sm:px-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div>
                                        <h3 class="text-sm font-bold text-yellow-800">
                                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Admin requested a time
                                            change
                                        </h3>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            <strong>Reason:</strong>
                                            {{ $appointment->reschedule_reason ?? 'Please pick a new time slot.' }}
                                        </p>
                                    </div>
                                    <button type="button"
                                        onclick="openUserRescheduleModal('{{ $appointment->id }}', '{{ $appointment->date }}', '{{ $appointment->time }}')"
                                        class="shrink-0 bg-yellow-400 px-4 py-2 rounded-md text-sm font-bold text-yellow-900 hover:bg-yellow-500 transition shadow-sm w-full sm:w-auto text-center">
                                        Pick New Time
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if ($upcomingAppointments->isEmpty())
                        <div class="p-10 text-center bg-white rounded-xl border border-dashed border-gray-200">
                            <div class="inline-flex bg-gray-50 p-4 rounded-full mb-3 text-gray-400">
                                <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No upcoming appointments.</p>
                            <a href="{{ route('appointments.create') }}"
                                class="text-blue-600 text-sm font-bold mt-2 inline-block hover:underline">
                                Book one now
                            </a>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-1 space-y-6">

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-50">
                            <h3 class="font-bold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('appointments.create') }}"
                                class="block w-full text-left px-4 py-3 rounded-lg hover:bg-blue-50 hover:text-blue-700 text-gray-700 text-sm font-medium transition group">
                                <div class="flex items-center">
                                    <span
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    New Appointment
                                </div>
                            </a>

                            <a href="{{ route('profile.edit') }}"
                                class="block w-full text-left px-4 py-3 rounded-lg hover:bg-blue-50 hover:text-blue-700 text-gray-700 text-sm font-medium transition group">
                                <div class="flex items-center">
                                    <span
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition">
                                        <i class="fa-regular fa-user"></i>
                                    </span>
                                    Edit Profile
                                </div>
                            </a>

                            <a href="{{ route('my.appointments') }}"
                                class="block w-full text-left px-4 py-3 rounded-lg hover:bg-blue-50 hover:text-blue-700 text-gray-700 text-sm font-medium transition group">
                                <div class="flex items-center">
                                    <span
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </span>
                                    View History
                                </div>
                            </a>

                            <a href="{{ route('complaint.create') }}"
                                class="block w-full text-left px-4 py-3 rounded-lg hover:bg-blue-50 hover:text-blue-700 text-gray-700 text-sm font-medium transition group">
                                <div class="flex items-center">
                                    <span
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </span>
                                    Complaint
                                </div>
                            </a>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-50 to-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-lightbulb text-purple-500 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-purple-900 text-sm">Pro Tip</h4>
                                <p class="text-xs text-purple-700 mt-1 leading-relaxed">
                                    Book appointments early to secure your preferred time slot! Slots fill up fast.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="userRescheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeUserRescheduleModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                <div class="bg-yellow-50 px-4 py-4 border-b border-yellow-200 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-yellow-900" id="modal-title">
                        <i class="fa-regular fa-calendar-check mr-2"></i> Pick New Appointment Time
                    </h3>
                    <button type="button" onclick="closeUserRescheduleModal()"
                        class="text-yellow-600 hover:text-yellow-800">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form id="userRescheduleForm" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="bg-white px-4 pt-5 pb-6 sm:p-6 space-y-5">
                        <p class="text-sm text-gray-500 mb-4">Please select a new date and time for your appointment. The
                            request will be sent back to the admin for approval.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">New Date</label>
                                <input type="date" name="date" id="modal_new_date" required
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full bg-white border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 sm:text-sm focus:ring-yellow-500 focus:border-yellow-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">New Time</label>
                                <input type="time" name="time" id="modal_new_time" required
                                    class="w-full bg-white border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 sm:text-sm focus:ring-yellow-500 focus:border-yellow-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-yellow-500 text-base font-bold text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Submit New Time
                        </button>
                        <button type="button" onclick="closeUserRescheduleModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUserRescheduleModal(id, oldDate, oldTime) {
            document.getElementById('modal_new_date').value = oldDate;
            document.getElementById('modal_new_time').value = oldTime;

            // Set the form action URL
            let form = document.getElementById('userRescheduleForm');

            // Updated to match your route perfectly!
            form.action = '/appointment/' + id + '/update-time';

            document.getElementById('userRescheduleModal').classList.remove('hidden');
        }

        function closeUserRescheduleModal() {
            document.getElementById('userRescheduleModal').classList.add('hidden');
        }
    </script>
@endsection
