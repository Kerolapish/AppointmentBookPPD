<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPD Kluang - Appointment System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F9FD;
        }

        .nav-active {
            color: #2563eb;
            font-weight: 600;
        }
    </style>
</head>

<body class="antialiased text-gray-800 relative">

    {{-- Watermark Logo Background --}}
    <div class="fixed inset-0 pointer-events-none opacity-[0.05] bg-center bg-no-repeat z-0"
        style="background-image: url('{{ asset('images/logoPPD.png') }}'); background-size: 500px;">
    </div>

    <div class="min-h-screen flex flex-col relative z-10">

        {{-- Navigation Bar --}}
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">

                    {{-- ZONE 1: Left Brand Section --}}
                    <div class="flex-shrink-0 flex items-center gap-2 lg:gap-3">
                        <img src="{{ asset('images/logoKPM.PNG') }}" alt="KPM Logo" class="h-11 w-auto object-contain">
                        <div class="leading-tight border-l-2 border-gray-200 pl-2 lg:pl-3">
                            <h1 class="font-bold text-gray-900 text-base lg:text-lg tracking-tight">PPD Kluang</h1>
                            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-bold">Appointment System
                            </p>
                        </div>
                    </div>

                    {{-- ZONE 2: Center Main Navigation Links --}}
                    <div
                        class="hidden sm:flex items-center justify-center space-x-1 md:space-x-2 lg:space-x-4 text-xs md:text-sm font-medium px-4">

                        {{-- ===== SUPER ADMIN LINKS ===== --}}
                        @if (Auth::check() && Auth::user()->role === 'super_admin')
                            <a href="{{ route('super_admin.dashboard') }}"
                                class="flex items-center gap-1 px-2 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('super_admin.dashboard') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-crown"></i> Master Dashboard
                            </a>
                            <a href="{{ route('super_admin.reports') }}"
                                class="flex items-center gap-1 px-2 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('super_admin.reports') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-chart-pie"></i> Reports
                            </a>
                            <a href="{{ route('super_admin.users') }}"
                                class="flex items-center gap-1 px-2 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('super_admin.users') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-users"></i> Users
                            </a>
                            <a href="{{ route('super_admin.availability') }}"
                                class="flex items-center gap-1 px-2 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('super_admin.availability*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fas fa-calendar-times"></i> Availability
                            </a>

                            {{-- ===== REGULAR ADMIN LINKS ===== --}}
                        @elseif (Auth::check() && Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-house"></i> Dashboard
                            </a>
                            <a href="{{ route('admin.requests') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('admin.requests') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-check"></i> Request
                            </a>
                            <a href="{{ route('admin.users') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('admin.users') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-user"></i> Users
                            </a>
                            <a href="{{ route('admin.availability') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('admin.availability') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fas fa-calendar-times"></i> Availability
                            </a>
                            <a href="{{ route('admin.appointments.active') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('admin.appointments.active') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-calendar-check"></i> Active Workspace
                            </a>
                            <a href="{{ route('admin.reports') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('admin.reports') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-file"></i> Report
                            </a>

                            {{-- ===== USER LINKS ===== --}}
                        @else
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-house"></i> Dashboard
                            </a>
                            <a href="{{ route('appointments.create') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('appointments.create') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-calendar-plus"></i> Book Appointment
                            </a>
                            <a href="{{ route('my.appointments') }}"
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition whitespace-nowrap {{ request()->routeIs('my.appointments') ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-list-check"></i> My Appointments
                            </a>
                        @endif
                    </div>

                    {{-- ZONE 3: Right User Profile Dropdown --}}
                    <div class="hidden sm:flex sm:items-center flex-shrink-0">
                        <div class="relative">
                            <button type="button" onclick="toggleUserDropdown()"
                                class="flex items-center gap-2 focus:outline-none transition hover:bg-gray-50 rounded-lg p-1.5 group"
                                id="user-menu-button">

                                <div class="text-right hidden md:block">
                                    <div
                                        class="text-xs font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition">
                                        {{ Auth::user()->name }}
                                    </div>
                                    <div
                                        class="text-[9px] uppercase font-bold px-2 py-0.5 rounded-full inline-block mt-0.5 
                                        {{ Auth::user()->role === 'super_admin' ? 'text-purple-600 bg-purple-50' : (Auth::user()->role === 'admin' ? 'text-blue-600 bg-blue-50' : 'text-green-600 bg-green-50') }}">
                                        {{ str_replace('_', ' ', Auth::user()->role) }}
                                    </div>
                                </div>

                                <div
                                    class="h-9 w-9 rounded-full border-2 border-white shadow-sm overflow-hidden bg-gray-200 ring-2 ring-transparent group-hover:ring-blue-100 transition flex-shrink-0">
                                    <img class="h-full w-full object-cover"
                                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&bold=true"
                                        alt="{{ Auth::user()->name }}">
                                </div>

                                <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition flex-shrink-0"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown Content --}}
                            <div id="user-dropdown-menu"
                                class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-lg py-2 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 transform transition-all duration-200 origin-top-right">
                                <div class="px-2 pt-1">
                                    <a href="{{ route('profile.show') }}"
                                        class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center mr-3 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                            <i class="fa-regular fa-user"></i>
                                        </div>
                                        My Profile
                                    </a>
                                </div>
                                <div class="border-t border-gray-100 my-1 mx-2"></div>
                                <div class="px-2 pb-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left group flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center mr-3 group-hover:bg-red-100 group-hover:text-red-600 transition-colors">
                                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                            </div>
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Menu Button --}}
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Content Area --}}
        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 mt-auto py-6">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs text-gray-400">© {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights
                    reserved.</p>
            </div>
        </footer>

    </div>

    {{-- Dropdown Scripts --}}
    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown-menu');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                dropdown.classList.add('opacity-100', 'scale-100');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        window.onclick = function(event) {
            const button = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('user-dropdown-menu');
            if (button && !button.contains(event.target) && dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        }
    </script>
</body>

</html>
