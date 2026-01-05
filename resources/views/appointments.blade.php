@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Book an Appointment</h2>
        <p class="text-sm text-gray-500 mt-1">Schedule your appointment with the Private Unit office</p>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-8 rounded-r-md flex items-start gap-3 shadow-sm">
        <i class="fa-solid fa-circle-info text-blue-600 mt-0.5"></i>
        <div>
            <h4 class="text-sm font-bold text-blue-900">Office Hours</h4>
            <p class="text-xs text-blue-700 mt-1">Monday - Friday: 8:00 AM - 5:00 PM | Lunch Break: 12:00 PM - 2:00 PM</p>
        </div>
    </div>

    <form action="#" method="POST"> 
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="space-y-6">
                
                <div class="bg-white border border-gray-100 rounded-lg p-6 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-4 text-xs uppercase tracking-wider">Availability Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                            <span class="text-gray-600">Today</span>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">1 slot available</span>
                        </div>
                        <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                            <span class="text-gray-600">Tomorrow</span>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">5 slots available</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">This Week</span>
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Limited slots</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-lg p-6 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-4 text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-yellow-500"></i> Booking Tips
                    </h3>
                    <ul class="space-y-3 text-xs text-gray-600 list-disc ml-4 leading-relaxed">
                        <li>Please arrive 5 minutes before your scheduled appointment time.</li>
                        <li>Bring all necessary documents related to your appointment.</li>
                        <li>To reschedule or cancel, please do so at least 24 hours in advance.</li>
                    </ul>
                </div>

            </div>

            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-100 rounded-lg p-8 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-6 text-lg border-b border-gray-100 pb-4">Appointment Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Select Date</label>
                            <div class="relative">
                                <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Select Time</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 bg-white">
                                <option>Select time...</option>
                                <option>9:00 AM</option>
                                <option>9:30 AM</option>
                                <option>10:00 AM</option>
                                <option>10:30 AM</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Full Name</label>
                        <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" 
                               placeholder="Enter your full name" 
                               value="{{ Auth::user()->name ?? '' }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Ips</label>
                            <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Ips">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Purpose of visit</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option>Select purpose...</option>
                                <option>Consultation</option>
                                <option>Document Submission</option>
                                <option>Administrative</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Location</label>
                        <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter location">
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-50">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-md shadow-md transition-all duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-check"></i> Confirm Booking
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </form>

@endsection