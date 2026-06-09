@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        .flatpickr-calendar.inline {
            width: 100% !important;
            box-shadow: none !important;
            border: none !important;
            background: transparent !important;
        }

        .flatpickr-innerContainer,
        .flatpickr-rContainer,
        .flatpickr-days,
        .dayContainer {
            width: 100% !important;
            max-width: 100% !important;
        }

        .flatpickr-day {
            max-width: 100% !important;
            height: 45px !important;
            line-height: 45px !important;
            border-radius: 12px !important;
        }

        .is-user-booked {
            background-color: #3b82f6 !important;
            color: white !important;
            font-weight: bold !important;
            border: 2px solid #2563eb !important;
        }

        .is-booked {
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
            opacity: 0.9 !important;
            border: 1px solid #fca5a5 !important;
        }

        .flatpickr-day.flatpickr-disabled, 
        .flatpickr-day.flatpickr-disabled:hover {
            color: #9ca3af !important;
            background: #f3f4f6 !important;
            cursor: not-allowed !important;
            border: none !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background-color: #eab308 !important;
            color: white !important;
            border-color: #ca8a04 !important;
        }

        /* Prevent parent overlays from swallowing mouse clicks */
        .overflow-hidden {
            isolation: isolate !important; 
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your appointments with Pejabat Pendidikan Daerah Kluang</p>
        </div>

        <div class="space-y-8">
            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                            <i class="fa-solid fa-calendar-check text-xl"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ ($percentageChange ?? 0) >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ($percentageChange ?? 0) > 0 ? '+' : '' }}{{ $percentageChange ?? 0 }}%
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $totalAppointments ?? 0 }}</h3>
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
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</h3>
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
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['confirmed'] ?? 0 }}</h3>
                        <p class="text-gray-500 text-sm">Confirmed</p>
                    </div>
                </div>

                {{-- Statistics Card transformed to show "Completed / Closed" counts --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>
                        <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2 py-1 rounded-full">Closed</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['completed'] ?? 0 }}</h3>
                        <p class="text-gray-500 text-sm">Completed</p>
                    </div>
                </div>
            </div>

            {{-- Content Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Left Column: Appointments List --}}
                <div class="lg:col-span-3 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Upcoming Appointments</h2>
                        <a href="{{ route('my.appointments') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium hover:underline flex items-center gap-1">
                            View All <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    @if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
                        @foreach ($upcomingAppointments as $appointment)
                            @php
                                $dateObj = \Carbon\Carbon::parse($appointment->date);
                                $timeObj = \Carbon\Carbon::parse($appointment->time);
                            @endphp

                            <div class="mb-4 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition {{ ($appointment->status === 'reschedule_requested' || $appointment->status === 'rescheduleRequested') ? 'border-yellow-400 ring-1 ring-yellow-400' : '' }}">
                                <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4 w-full">
                                        <div class="bg-blue-50 text-blue-600 px-4 py-3 rounded-xl text-center min-w-[70px] border border-blue-100">
                                            <span class="block text-xs font-bold uppercase tracking-wider">{{ $dateObj->format('M') }}</span>
                                            <span class="block text-xl font-bold">{{ $dateObj->format('d') }}</span>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900">{{ $appointment->purpose }}</h3>
                                            <p class="text-xs text-blue-500 font-semibold mb-1">{{ $appointment->ips }}</p>
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                                <span><i class="fa-regular fa-clock mr-1"></i> {{ $timeObj->format('h:i A') }}</span>
                                                <span class="hidden sm:inline text-gray-300">|</span>
                                                <span><i class="fa-solid fa-location-dot mr-1"></i> {{ $appointment->location }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        {{-- Updated to securely match both underscore and camelCase status variants --}}
                                        @if($appointment->status === 'approved')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1.5 rounded-full">Approved</span>
                                        @elseif($appointment->status === 'reschedule_requested' || $appointment->status === 'rescheduleRequested')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1.5 rounded-full">Reschedule Requested</span>
                                        @elseif($appointment->status === 'rejected')
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1.5 rounded-full">Rejected</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1.5 rounded-full">{{ ucfirst($appointment->status) }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- FIXED: Added handling for camelCase strings ('rescheduleRequested') --}}
                                @if ($appointment->status === 'reschedule_requested' || $appointment->status === 'rescheduleRequested')
                                    <div class="bg-yellow-50 border-t border-yellow-200 p-4 sm:px-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                                        <div class="relative z-10">
                                            <h3 class="text-sm font-bold text-yellow-800">
                                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Admin requested a time change
                                            </h3>
                                            <p class="mt-1 text-sm text-yellow-700">
                                                <strong>Reason:</strong> {{ $appointment->reschedule_reason ?? 'Please pick a new time slot.' }}
                                            </p>
                                        </div>
                                        <div class="relative z-30 shrink-0 w-full sm:w-auto">
                                            <button type="button" 
                                                onclick="openUserRescheduleModal('{{ $appointment->id }}', '{{ $appointment->date }}', '{{ $appointment->time }}')"
                                                class="cursor-pointer block w-full bg-yellow-400 px-5 py-2.5 rounded-xl text-sm font-bold text-yellow-900 hover:bg-yellow-500 active:scale-95 transition shadow-sm text-center">
                                                Pick New Time
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="bg-white p-8 rounded-xl border text-center text-gray-500">
                            No upcoming appointments scheduled.
                        </div>
                    @endif
                </div>

                {{-- Sidebar Column --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('appointments.create') }}" class="flex items-center gap-3 w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl font-medium transition text-sm">
                                <i class="fa-solid fa-circle-plus"></i> New Appointment
                            </a>
                            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl font-medium transition text-sm">
                                <i class="fa-solid fa-user-gear"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reschedule Modal Markup Instance --}}
    <div id="userRescheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60" onclick="closeUserRescheduleModal()"></div>
            <div class="relative bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl">
                <div class="bg-yellow-50 px-6 py-5 border-b flex justify-between items-center">
                    <h3 class="text-xl font-bold text-yellow-900">Select New Appointment Time</h3>
                    <button onclick="closeUserRescheduleModal()" class="text-yellow-600 text-2xl">&times;</button>
                </div>

                <form id="userRescheduleForm" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="p-8 grid grid-cols-1 md:grid-cols-12 gap-8">
                        <div class="md:col-span-7">
                            <label class="block font-bold mb-3 text-gray-700">1. SELECT DATE</label>
                            <input type="text" id="rescheduleCalendar" class="w-full hidden">
                            <input type="hidden" name="date" id="modal_new_date" required>
                        </div>

                        <div class="md:col-span-5">
                            <label class="block font-bold mb-3 text-gray-700">2. AVAILABLE SLOTS</label>
                            <select name="time" id="modal_new_time" required class="w-full border rounded-xl py-3 px-4 focus:ring-2 focus:ring-yellow-400 outline-none">
                                <option value="">Select a date first...</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-5 border-t flex justify-end gap-3">
                        <button type="button" onclick="closeUserRescheduleModal()" class="px-6 py-2 border rounded-xl">Cancel</button>
                        <button type="submit" class="px-8 py-2 bg-yellow-500 text-white rounded-xl font-bold hover:bg-yellow-600">Submit New Time</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const blockedDates = @json($blockedDates ?? []);
        const fullyBookedDates = @json($fullyBookedDates ?? []);
        const userBookedDates = @json($userBookedDates ?? []);
        const hourlySlots = ["08:00", "09:00", "10:00", "11:00", "12:00", "14:00", "15:00"];

        let rescheduleFp;

        function getLocalDateString(dateObj) {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function openUserRescheduleModal(id, oldDate, oldTime) {
            const form = document.getElementById('userRescheduleForm');
            form.action = `/appointment/${id}/update-time`;

            rescheduleFp = flatpickr("#rescheduleCalendar", {
                inline: true,
                minDate: "today",
                defaultDate: oldDate || "today",
                dateFormat: "Y-m-d",
                disable: [
                    function(date) {
                        if (date.getDay() === 0 || date.getDay() === 6) return true;
                        return [...blockedDates, ...fullyBookedDates].includes(getLocalDateString(date));
                    }
                ],
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const dateStr = getLocalDateString(dayElem.dateObj);
                    dayElem.classList.remove("flatpickr-disabled");

                    if (userBookedDates.includes(dateStr)) {
                        dayElem.classList.add("is-user-booked");
                    } else if (fullyBookedDates.includes(dateStr) || blockedDates.includes(dateStr)) {
                        dayElem.classList.add("is-booked");
                    }
                },
                onChange: function(selectedDates, dateStr) {
                    document.getElementById('modal_new_date').value = dateStr;
                    updateRescheduleTimeSlots(dateStr);
                }
            ]);

            document.getElementById('userRescheduleModal').classList.remove('hidden');

            if (oldDate) {
                document.getElementById('modal_new_date').value = oldDate;
                updateRescheduleTimeSlots(oldDate);
            }
        }

        async function updateRescheduleTimeSlots(date) {
            const timeSelect = document.getElementById('modal_new_time');
            timeSelect.innerHTML = '<option value="">Loading slots...</option>';

            try {
                const response = await fetch(`{{ route('appointments.booked-times') }}?date=${date}`);
                const bookedTimes = await response.json();

                timeSelect.innerHTML = '<option value="">Select Time...</option>';
                hourlySlots.forEach(time => {
                    let option = document.createElement('option');
                    option.value = time;

                    let [hours] = time.split(':');
                    let startHour = parseInt(hours);
                    let endHour = startHour + 1;
                    let startAmPm = startHour >= 12 ? 'PM' : 'AM';
                    let endAmPm = endHour >= 12 ? 'PM' : 'AM';
                    let displayStartHour = startHour % 12 || 12;
                    let displayEndHour = endHour % 12 || 12;

                    const isBooked = bookedTimes.includes(time);
                    option.text = `${displayStartHour}:00 ${startAmPm} - ${displayEndHour}:00 ${endAmPm}`;

                    if (isBooked) {
                        option.disabled = true;
                        option.text += ' (Fully Booked)';
                        option.classList.add('text-red-400');
                    }
                    timeSelect.appendChild(option);
                });
            } catch (error) {
                timeSelect.innerHTML = '<option value="">Error loading times</option>';
            }
        }

        function closeUserRescheduleModal() {
            document.getElementById('userRescheduleModal').classList.add('hidden');
            if (rescheduleFp) rescheduleFp.destroy();
        }
    </script>
@endsection