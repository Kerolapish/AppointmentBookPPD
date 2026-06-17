@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        /* Admin Blocked Dates AND Fully Booked Dates */
        .flatpickr-day.is-blocked,
        .flatpickr-day.is-blocked.flatpickr-disabled,
        .flatpickr-day.is-blocked.flatpickr-disabled:hover,
        .flatpickr-day.is-booked,
        .flatpickr-day.is-booked.flatpickr-disabled,
        .flatpickr-day.is-booked.flatpickr-disabled:hover {
            background-color: #fee2e2 !important;
            /* Light red */
            color: #dc2626 !important;
            /* Bold red text */
            border-color: #fca5a5 !important;
            /* Red border */
        }

        /* Dates the CURRENT USER has already booked */
        .flatpickr-day.is-user-booked,
        .flatpickr-day.is-user-booked.flatpickr-disabled,
        .flatpickr-day.is-user-booked.flatpickr-disabled:hover {
            background-color: #dbeafe !important;
            /* Light blue */
            color: #1e3a8a !important;
            /* Dark blue text */
            border-color: #bfdbfe !important;
            /* Blue border */
        }

        /* Available Dates */
        .flatpickr-day.is-available {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
            color: #374151 !important;
        }
    </style>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Book an Appointment</h2>
        <p class="text-sm text-gray-500 mt-1">Schedule your appointment with the Private Unit office</p>
    </div>

    <div class="bg-gray-200 border-l-4 border-gray-600 p-4 mb-8 rounded-r-md flex items-center shadow-sm">
        <div class="mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-bold text-gray-800">Office Hours & Rules</h4>
            <p class="text-xs text-gray-700">Monday - Friday: 8:00 AM - 4:00 PM</p>
            <p class="text-xs font-semibold text-blue-700 mt-0.5">Strictly 1 hour per session. Maximum 5 slots available per
                day.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm border border-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1 space-y-6">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Availability Status</h3>
                <div class="space-y-4 text-sm">

                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-gray-600">Today</span>
                        @if ($todayLeft > 0)
                            <span class="font-semibold text-green-600">{{ $todayLeft }}
                                slot{{ $todayLeft > 1 ? 's' : '' }} available</span>
                        @else
                            <span class="font-semibold text-red-600">Fully Booked</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <span class="text-gray-600">Tomorrow</span>
                        @if ($tomorrowLeft > 0)
                            <span class="font-semibold text-green-600">{{ $tomorrowLeft }}
                                slot{{ $tomorrowLeft > 1 ? 's' : '' }} available</span>
                        @else
                            <span class="font-semibold text-red-600">Fully Booked</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">This Week</span>
                        <span class="font-semibold {{ $weekColor }}">{{ $weekStatus }}</span>
                    </div>

                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Booking Tips</h3>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-2">
                    <li>Please arrive 5 minutes before your scheduled time.</li>
                    <li>Bring all necessary documents related to your appointment.</li>
                    <li>To reschedule or cancel, please do so at least 24 hours in advance.</li>
                    <li class="text-blue-700 font-medium">New slots open up automatically every day for a rolling 30-day
                        window.</li>
                </ul>
            </div>

        </div>

        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 h-full">
                <h3 class="font-bold text-gray-900 mb-6 text-lg">Appointment Details</h3>

                <form action="{{ route('appointments.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Select Date</label>
                            <input type="text" name="date" id="dateInput" required placeholder="Select a date below"
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition hidden">

                            <div class="mt-4 grid grid-cols-2 gap-2 text-[10px] font-semibold uppercase tracking-wider">
                                <div
                                    class="flex items-center gap-2 p-2 rounded bg-blue-50 border border-blue-100 text-blue-700">
                                    <span class="w-3 h-3 rounded-full bg-[#dbeafe] border border-[#bfdbfe]"></span>
                                    <span>Your Bookings</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 p-2 rounded bg-red-50 border border-red-100 text-red-700">
                                    <span class="w-3 h-3 rounded-full bg-[#fee2e2] border border-[#fca5a5]"></span>
                                    <span>Unavailable / Full</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 p-2 rounded bg-gray-50 border border-gray-200 text-gray-500">
                                    <span class="w-3 h-3 rounded-full bg-white border border-gray-300"></span>
                                    <span>Available Slots</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 p-2 rounded bg-gray-100 border border-gray-200 text-gray-600">
                                    <span class="w-3 h-3 rounded-full bg-gray-200"></span>
                                    <span>Weekends</span>
                                </div>
                            </div>

                            <p id="dateError" class="text-red-500 text-xs mt-2 hidden">Please select a valid weekday.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Select Time</label>
                            <select name="time" id="timeSelect" required disabled
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition disabled:bg-gray-200 disabled:cursor-not-allowed">
                                <option value="">Select a date first...</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" readonly
                            class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed"
                            value="{{ Auth::user()->name }}">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" required
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition"
                            value="{{ Auth::user()->phone }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">IPS (Institusi)</label>
                            <input type="text" name="ips" required placeholder="e.g. Tadika Pintar"
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition"
                                value="{{ Auth::user()->ips_name }}">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Purpose of visit</label>

                            <select name="purpose" id="purposeSelect" required onchange="toggleOtherPurpose()"
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Select Purpose...</option>
                                <option value="Consultation">Consultation</option>
                                <option value="Document Submission">Document Submission</option>
                                <option value="Application Renewal">Renew Permit</option>
                                <option value="Visit">Visit</option>
                                <option value="Other">Other</option>
                            </select>

                            <div id="otherPurposeContainer" class="hidden mt-3">
                                <input type="text" name="other_purpose" id="otherPurposeInput"
                                    placeholder="Please specify your purpose..."
                                    class="w-full bg-blue-50 border border-blue-300 text-blue-900 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition">
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Location</label>
                        <input type="text" name="location" required placeholder="e.g. Kaunter Utama"
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-gray-800 hover:bg-black text-white font-bold py-2.5 px-8 rounded-lg shadow-lg flex items-center gap-2 transition transform hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Confirm Booking
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleOtherPurpose() {
            var select = document.getElementById("purposeSelect");
            var otherDiv = document.getElementById("otherPurposeContainer");
            var otherInput = document.getElementById("otherPurposeInput");

            if (select.value === "Other") {
                otherDiv.classList.remove("hidden");
                otherInput.setAttribute("required", "required");
            } else {
                otherDiv.classList.add("hidden");
                otherInput.removeAttribute("required");
                otherInput.value = "";
            }
        }
    </script>

    <script>
        const blockedDates = @json($blockedDates ?? []);
        const fullyBookedDates = @json($fullyBookedDates ?? []);
        const userBookedDates = @json($userBookedDates ?? []);
        const allUnavailableDates = [...blockedDates, ...fullyBookedDates, ...userBookedDates];

        function parseLocalDateString(date) {
            return date.getFullYear() + "-" +
                String(date.getMonth() + 1).padStart(2, '0') + "-" +
                String(date.getDate()).padStart(2, '0');
        }

        flatpickr("#dateInput", {
            inline: true,
            minDate: "today",
            maxDate: new Date().fp_incr(30),
            disable: [
                function(date) {
                    return (date.getDay() === 0 || date.getDay() === 6);
                },
                function(date) {
                    return allUnavailableDates.includes(parseLocalDateString(date));
                }
            ],
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                let formattedDate = parseLocalDateString(dayElem.dateObj);
                let today = new Date();
                today.setHours(0,0,0,0);

                // Only colorize dates that are within the active booking window (today and future)
                if (dayElem.dateObj >= today) {
                    if (userBookedDates.includes(formattedDate)) {
                        dayElem.classList.add("is-user-booked");
                        dayElem.title = "You already have an appointment on this date!";
                    } else if (blockedDates.includes(formattedDate)) {
                        dayElem.classList.add("is-blocked");
                        dayElem.title = "Office closed";
                    } else if (fullyBookedDates.includes(formattedDate)) {
                        dayElem.classList.add("is-booked");
                        dayElem.title = "Fully booked";
                    } else {
                        let isWeekend = dayElem.dateObj.getDay() === 0 || dayElem.dateObj.getDay() === 6;
                        if (!isWeekend) {
                            dayElem.classList.add("is-available");
                            dayElem.title = "Available";
                        }
                    }
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('dateInput').value = dateStr;
                updateTimeSlots();
            }
        });
    </script>

    <script>
        const hourlySlots = ["08:00", "09:00", "10:00", "11:00", "12:00", "14:00", "15:00"];

        async function updateTimeSlots() {
            const dateInput = document.getElementById('dateInput');
            const timeSelect = document.getElementById('timeSelect');

            timeSelect.innerHTML = '<option value="">Loading availability...</option>';
            timeSelect.disabled = true;

            if (!dateInput.value) return;

            try {
                const response = await fetch(`{{ route('appointments.booked-times') }}?date=${dateInput.value}`);
                const bookedTimes = await response.json();

                timeSelect.innerHTML = '<option value="">Select Time...</option>';

                hourlySlots.forEach(time => {
                    let option = document.createElement('option');
                    option.value = time;

                    const isBooked = bookedTimes.includes(time);

                    let [hours] = time.split(':');
                    let startHour = parseInt(hours);
                    let endHour = startHour + 1;
                    let startAmPm = startHour >= 12 ? 'PM' : 'AM';
                    let endAmPm = endHour >= 12 ? 'PM' : 'AM';
                    let displayStartHour = startHour % 12 || 12;
                    let displayEndHour = endHour % 12 || 12;

                    option.text = `${displayStartHour}:00 ${startAmPm} - ${displayEndHour}:00 ${endAmPm}`;

                    if (isBooked) {
                        option.disabled = true;
                        option.classList.add('text-gray-400', 'bg-gray-100');
                    }
                    timeSelect.appendChild(option);
                });

                timeSelect.disabled = false;

            } catch (error) {
                timeSelect.innerHTML = '<option value="">Error loading times</option>';
            }
        }
    </script>

@endsection
