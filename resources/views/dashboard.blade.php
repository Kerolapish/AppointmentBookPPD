@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Welcome back, Ahmad!</h2>
        <p class="text-gray-500 mt-1">Manage your appointments with Pejabat Pendidikan Daerah Kluang</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card-base flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="icon-square icon-blue"><i class="fa-solid fa-calendar-check"></i></div>
                <span class="status-pill status-green">+12%</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800">8</h3>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Total Appointments</p>
            </div>
        </div>

        <div class="card-base flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="icon-square icon-yellow"><i class="fa-solid fa-clock"></i></div>
                <span class="status-pill status-yellow">Pending</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800">2</h3>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Pending Approval</p>
            </div>
        </div>

        <div class="card-base flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="icon-square icon-green"><i class="fa-solid fa-check-circle"></i></div>
                <span class="text-green-600 text-xs font-bold">Active</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800">5</h3>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Confirmed</p>
            </div>
        </div>

        <div class="card-base flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="icon-square icon-purple"><i class="fa-solid fa-calendar-day"></i></div>
                <span class="text-purple-600 text-xs font-bold">This Month</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800">3</h3>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Upcoming</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Upcoming Appointments</h3>
                <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-800">View All</a>
            </div>

            <div class="space-y-3">
                <div class="list-item-card">
                    <div class="flex items-center">
                        <div class="date-badge">
                            <span class="day">15</span><span class="month">JAN</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Consultation - School Program</h4>
                            <p class="text-xs text-blue-500 font-semibold mb-1">Unit Pendidikan Khas</p>
                            <div class="text-xs text-gray-400 flex items-center gap-3">
                                <span><i class="fa-regular fa-clock mr-1"></i> 10:00 AM</span>
                                <span><i class="fa-solid fa-location-dot mr-1"></i> Bilik Mesyuarat 1</span>
                            </div>
                        </div>
                    </div>
                    <span class="status-pill status-green hidden sm:block">Confirmed</span>
                </div>

                <div class="list-item-card">
                    <div class="flex items-center">
                        <div class="date-badge">
                            <span class="day">18</span><span class="month">JAN</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Document Submission</h4>
                            <p class="text-xs text-blue-500 font-semibold mb-1">Unit Pentadbiran</p>
                            <div class="text-xs text-gray-400 flex items-center gap-3">
                                <span><i class="fa-regular fa-clock mr-1"></i> 2:00 PM</span>
                                <span><i class="fa-solid fa-location-dot mr-1"></i> Pejabat Utama</span>
                            </div>
                        </div>
                    </div>
                    <span class="status-pill status-yellow hidden sm:block">Pending</span>
                </div>

                <div class="list-item-card">
                    <div class="flex items-center">
                        <div class="date-badge">
                            <span class="day">22</span><span class="month">JAN</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Training Session</h4>
                            <p class="text-xs text-blue-500 font-semibold mb-1">Unit Latihan</p>
                            <div class="text-xs text-gray-400 flex items-center gap-3">
                                <span><i class="fa-regular fa-clock mr-1"></i> 9:00 AM</span>
                                <span><i class="fa-solid fa-location-dot mr-1"></i> Dewan Seminar</span>
                            </div>
                        </div>
                    </div>
                    <span class="status-pill status-green hidden sm:block">Confirmed</span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card-base">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="#" class="btn-primary">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-plus-circle"></i> New Appointment</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    
                    <button class="w-full flex justify-between items-center p-3 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-100 font-medium">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-user-pen text-gray-400"></i> Edit Profile</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
                    </button>
                    <button class="w-full flex justify-between items-center p-3 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-100 font-medium">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-clock-rotate-left text-gray-400"></i> View History</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
                    </button>
                    <button class="w-full flex justify-between items-center p-3 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-100 font-medium">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-headset text-gray-400"></i> Help & Support</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-300"></i>
                    </button>
                </div>
            </div>

            <div class="tip-box">
                <div class="text-purple-600"><i class="fa-solid fa-lightbulb text-xl"></i></div>
                <div>
                    <h4 class="text-sm font-bold text-purple-900">Pro Tip</h4>
                    <p class="text-xs text-purple-700 mt-1 leading-relaxed">Book appointments early to secure your preferred time slot!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Available Units for Booking</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card-base cursor-pointer group hover:border-blue-300">
                <div class="icon-square icon-blue mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-graduation-cap text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">Unit Pendidikan Khas</h4>
                <p class="text-xs text-gray-500 mt-2 mb-4 h-8 overflow-hidden">Special education programs and consultations</p>
                <div class="text-xs text-gray-500 font-medium"><i class="fa-regular fa-clock mr-1"></i> 60 min</div>
            </div>

            <div class="card-base cursor-pointer group hover:border-green-300">
                <div class="icon-square icon-green mb-4 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-file-lines text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">Unit Pentadbiran</h4>
                <p class="text-xs text-gray-500 mt-2 mb-4 h-8 overflow-hidden">Administrative services and document processing</p>
                <div class="text-xs text-gray-500 font-medium"><i class="fa-regular fa-clock mr-1"></i> 30 min</div>
            </div>

            <div class="card-base cursor-pointer group hover:border-purple-300">
                <div class="icon-square icon-purple mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-chalkboard-user text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">Unit Latihan</h4>
                <p class="text-xs text-gray-500 mt-2 mb-4 h-8 overflow-hidden">Professional development and training sessions</p>
                <div class="text-xs text-gray-500 font-medium"><i class="fa-regular fa-clock mr-1"></i> 90 min</div>
            </div>

            <div class="card-base cursor-pointer group hover:border-yellow-300">
                <div class="icon-square icon-yellow mb-4 group-hover:bg-yellow-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-users-gear text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">Unit Kurikulum</h4>
                <p class="text-xs text-gray-500 mt-2 mb-4 h-8 overflow-hidden">Curriculum planning and development</p>
                <div class="text-xs text-gray-500 font-medium"><i class="fa-regular fa-clock mr-1"></i> 45 min</div>
            </div>
        </div>
    </div>

@endsection