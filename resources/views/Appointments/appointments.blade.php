@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')

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
            <h4 class="text-sm font-bold text-gray-800">Office Hours</h4>
            <p class="text-xs text-gray-700">Monday - Thursday: 8:00 AM - 1:00 PM | 2:00 PM - 5:00 PM</p>
            <p class="text-xs text-gray-700">Friday: 8:00 AM - 12:15 PM | 2:45 PM - 5:00 PM</p>
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
                            <label class="block text-xs font-bold text-gray-700 mb-2">Select Date (Mon-Fri)</label>
                            <input type="date" name="date" id="dateInput" required min="{{ date('Y-m-d') }}"
                                onchange="updateTimeSlots()"
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <p id="dateError" class="text-red-500 text-xs mt-1 hidden">Please select a weekday (Mon-Fri).
                            </p>
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
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition">
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
                otherInput.setAttribute("required", "required"); // Make it required if shown
            } else {
                otherDiv.classList.add("hidden");
                otherInput.removeAttribute("required"); // Remove required if hidden
                otherInput.value = ""; // Clear input if user switches back
            }
        }
    </script>

    <script>
        // Define time slots based on the image (using 30-minute intervals)
        const monThuSlots = [
            // Morning: 8am - 1pm
            "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30",
            // Afternoon: 2pm - 5pm (Last slot starts at 4:30pm assuming 30m duration)
            "14:00", "14:30", "15:00", "15:30", "16:00", "16:30"
        ];

        const fridaySlots = [
            // Morning: 8am - 12:15pm
            "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
            // Afternoon: 2:45pm - 5pm
            "14:45", "15:15", "15:45", "16:15", "16:45"
        ];

        function updateTimeSlots() {
            const dateInput = document.getElementById('dateInput');
            const timeSelect = document.getElementById('timeSelect');
            const dateError = document.getElementById('dateError');

            // Reset time select
            timeSelect.innerHTML = '<option value="">Select Time...</option>';
            timeSelect.disabled = true;
            dateError.classList.add('hidden');

            if (!dateInput.value) {
                return;
            }

            const selectedDate = new Date(dateInput.value + "T00:00:00");
            const dayOfWeek = selectedDate.getDay(); // 0=Sun, 1=Mon, ..., 6=Sat

            // 1. Check if Weekend (Saturday or Sunday)
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                dateError.classList.remove('hidden');
                dateInput.value = ''; // Clear the invalid date
                return;
            }

            // 2. Determine allowed slots based on day
            let allowedSlots = [];
            if (dayOfWeek === 5) { // Friday
                allowedSlots = fridaySlots;
            } else { // Monday (1) to Thursday (4)
                allowedSlots = monThuSlots;
            }

            // 3. Populate the dropdown
            allowedSlots.forEach(time => {
                let option = document.createElement('option');
                option.value = time;
                // Format time for display (e.g., "08:00" -> "8:00 AM")
                let [hours, minutes] = time.split(':');
                let ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                option.text = `${hours}:${minutes} ${ampm}`;
                timeSelect.appendChild(option);
            });

            timeSelect.disabled = false;
        }
    </script>

@endsection
