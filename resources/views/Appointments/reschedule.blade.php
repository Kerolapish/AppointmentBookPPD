@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6 text-white flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold">Reschedule Appointment</h1>
                        <p class="text-blue-100 text-sm mt-1">ID: #{{ $appointment->id }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 border-b border-orange-100 px-8 py-4 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-orange-500 mt-0.5"></i>
                <p class="text-sm text-orange-800">
                    <strong>Note:</strong> Rescheduling will reset your status to <span class="uppercase font-bold text-xs bg-orange-200 text-orange-800 px-2 py-0.5 rounded">Pending</span>. The admin will need to approve the new time.
                </p>
            </div>

            <form action="{{ route('appointments.updateReschedule', $appointment->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Current Schedule</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 block mb-1">Service Type</label>
                                <div class="font-medium text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-briefcase text-blue-500"></i>
                                    {{ $appointment->purpose }}
                                </div>
                            </div>
                            
                            <div class="h-px bg-gray-200"></div>

                            <div>
                                <label class="text-xs text-gray-500 block mb-1">Current Date</label>
                                <div class="font-medium text-gray-800 flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-blue-500"></i>
                                    {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}
                                </div>
                            </div>

                            <div>
                                <label class="text-xs text-gray-500 block mb-1">Current Time</label>
                                <div class="font-medium text-gray-800 flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-blue-500"></i>
                                    {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">New Preference</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">New Date <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
                                <div class="relative">
                                    <i class="fa-regular fa-calendar-check absolute left-4 top-3.5 text-gray-400"></i>
                                    <input type="date" name="new_date" min="{{ date('Y-m-d') }}"
                                        class="w-full pl-11 pr-4 py-3 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-shadow text-gray-700 cursor-pointer">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Leave blank to keep current date.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">New Time <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
                                <div class="relative">
                                    <i class="fa-regular fa-clock absolute left-4 top-3.5 text-gray-400"></i>
                                    <input type="time" name="new_time"
                                        class="w-full pl-11 pr-4 py-3 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-shadow text-gray-700 cursor-pointer">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Leave blank to keep current time.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                    <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-lg shadow-blue-200 hover:shadow-blue-300 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        Confirm Reschedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection