@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h1>
        <p class="text-gray-500 text-sm mt-1">Manage your appointments with Pejabat Pendidikan Daerah Kluang</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <span class="bg-green-50 text-green-600 text-[10px] font-bold px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total'] }}</h3>
            <p class="text-xs text-gray-500 font-medium">Total Appointments</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <span class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-1 rounded-full">Pending</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['pending'] }}</h3>
            <p class="text-xs text-gray-500 font-medium">Pending Approval</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
                <span class="bg-green-50 text-green-600 text-[10px] font-bold px-2 py-1 rounded-full">Active</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['confirmed'] }}</h3>
            <p class="text-xs text-gray-500 font-medium">Confirmed</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fa-regular fa-calendar"></i>
                </div>
                <span class="bg-purple-50 text-purple-600 text-[10px] font-bold px-2 py-1 rounded-full">This Month</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['upcoming'] }}</h3>
            <p class="text-xs text-gray-500 font-medium">Upcoming</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

        <div class="lg:col-span-2 space-y-6">
            <div class="flex justify-between items-center mb-2">
                <h2 class="font-bold text-gray-800 text-lg">Upcoming Appointments</h2>
                {{-- Make sure this route exists in web.php or remove the link --}}
                <a href="#" class="text-sm text-blue-600 font-semibold hover:underline">View All</a>
            </div>

            @foreach ($upcomingAppointments as $appointment)
                @php
                    $dateObj = \Carbon\Carbon::parse($appointment->date);
                    $timeObj = \Carbon\Carbon::parse($appointment->time);
                @endphp

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row gap-5 items-start sm:items-center">
                    <div class="flex-shrink-0 w-16 h-16 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center border border-blue-100">
                        <span class="text-xl font-bold leading-none">{{ $dateObj->format('d') }}</span> 
                        <span class="text-[10px] font-bold uppercase mt-1">{{ $dateObj->format('M') }}</span>
                    </div>

                    <div class="flex-grow">
                        <h4 class="font-bold text-gray-900">{{ $appointment->purpose }}</h4>
                        
                        <p class="text-xs text-blue-500 font-semibold mb-2">{{ $appointment->ips }}</p>
                        
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                            <span><i class="fa-regular fa-clock mr-1"></i>
                                {{ $timeObj->format('h:i A') }}
                            </span>
                            <span><i class="fa-solid fa-location-dot mr-1"></i> {{ $appointment->location }}</span>
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        @if ($appointment->status == 'confirmed')
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-lg">Confirmed</span>
                        @elseif($appointment->status == 'pending')
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-lg">Pending</span>
                        @else
                            <span class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1.5 rounded-lg">{{ ucfirst($appointment->status) }}</span>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($upcomingAppointments->isEmpty())
                <div class="p-10 text-center bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="inline-flex bg-gray-50 p-4 rounded-full mb-3 text-gray-400">
                        <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No upcoming appointments.</p>
                    {{-- Ensure route name matches your web.php --}}
                    <a href="{{ route('appointments') }}" class="text-blue-600 text-sm font-bold mt-2 inline-block">Book one now</a>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-6">
            <h2 class="font-bold text-gray-800 text-lg">Quick Actions</h2>

            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-3">
                {{-- UPDATED: Route name --}}
                <a href="{{ route('appointments.create') }}"
                    class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition flex justify-between items-center shadow-md shadow-blue-200">
                    <span class="flex items-center gap-2"><i class="fa-solid fa-plus-circle"></i> New Appointment</span>
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </a>

                <a href="#" class="block w-full bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-lg transition flex justify-between items-center border border-gray-100">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-user-pen text-gray-400"></i> Edit Profile</span>
                    <i class="fa-solid fa-arrow-right text-gray-300 text-xs"></i>
                </a>

                <a href="#" class="block w-full bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-lg transition flex justify-between items-center border border-gray-100">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-clock-rotate-left text-gray-400"></i> View History</span>
                    <i class="fa-solid fa-arrow-right text-gray-300 text-xs"></i>
                </a>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-white p-5 rounded-xl border border-purple-100 shadow-sm">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-lightbulb text-purple-500 text-xl mt-1"></i>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">Pro Tip</h4>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Book appointments early to secure your preferred time slot! Slots fill up fast.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h2 class="font-bold text-gray-800 text-lg mb-4">Available Units for Booking</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h4 class="font-bold text-gray-900 text-sm">Unit Pendidikan Khas</h4>
                <p class="text-[10px] text-gray-500 mt-1 mb-3">Special education programs and consultations</p>
                <div class="text-xs text-gray-400 font-medium"><i class="fa-regular fa-clock"></i> 60 min</div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <h4 class="font-bold text-gray-900 text-sm">Unit Pentadbiran</h4>
                <p class="text-[10px] text-gray-500 mt-1 mb-3">Administrative services and document processing</p>
                <div class="text-xs text-gray-400 font-medium"><i class="fa-regular fa-clock"></i> 30 min</div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-600 group-hover:text-white transition">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <h4 class="font-bold text-gray-900 text-sm">Unit Latihan</h4>
                <p class="text-[10px] text-gray-500 mt-1 mb-3">Professional development and training sessions</p>
                <div class="text-xs text-gray-400 font-medium"><i class="fa-regular fa-clock"></i> 90 min</div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center mb-3 group-hover:bg-orange-600 group-hover:text-white transition">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <h4 class="font-bold text-gray-900 text-sm">Unit Kurikulum</h4>
                <p class="text-[10px] text-gray-500 mt-1 mb-3">Curriculum planning and development</p>
                <div class="text-xs text-gray-400 font-medium"><i class="fa-regular fa-clock"></i> 45 min</div>
            </div>
        </div>
    </div>
@endsection