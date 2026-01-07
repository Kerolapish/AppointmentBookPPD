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
            <p class="text-xs text-gray-700">Monday - Friday: 8:00 AM - 5:00 PM | Lunch Break: 12:00 PM - 2:00 PM</p>
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
                            <label class="block text-xs font-bold text-gray-700 mb-2">Select Date</label>
                            <input type="date" name="date" required
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Select Time</label>
                            <input type="time" name="time" required
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
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
                        <input type="text" name="phone" required placeholder="e.g. 012-3456789"
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">IPS (Institusi)</label>
                            <input type="text" name="ips" required placeholder="e.g. Tadika Pintar"
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Purpose of visit</label>
                            <select name="purpose" required
                                class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Select Purpose...</option>
                                <option value="Consultation">Consultation</option>
                                <option value="Document Submission">Document Submission</option>
                                <option value="Application Renewal">Application Renewal</option>
                                <option value="Other">Other</option>
                            </select>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Confirm Booking
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection
