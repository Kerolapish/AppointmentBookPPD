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
                            class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:shadow-md transition">
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
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1.5 rounded-full">{{ ucfirst($appointment->status) }}</span>
                                @endif
                            </div>
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
                                class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 text-gray-700 text-sm font-medium transition group">
                                <div class="flex items-center">
                                    <span
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center mr-3 group-hover:bg-gray-200 transition">
                                        <i class="fa-regular fa-user"></i>
                                    </span>
                                    Edit Profile
                                </div>
                            </a>

                            <a href="{{ route('my.appointments') }}"
                                class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 text-gray-700 text-sm font-medium transition group">
                                <div class="flex items-center">
                                    <span
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center mr-3 group-hover:bg-gray-200 transition">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </span>
                                    View History
                                </div>
                            </a>

                            <a href="{{ route('complaint.create') }}"
                                class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 text-gray-700 text-sm font-medium transition group">
                                <div class="flex items-center">
                                    <span
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center mr-3 group-hover:bg-gray-200 transition">
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
@endsection
