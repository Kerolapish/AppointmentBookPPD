<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPD Kluang - Appointment System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

<body class="antialiased text-gray-800">

    <div class="min-h-screen flex flex-col">

        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    <div class="flex items-center gap-8">
                        <div class="flex-shrink-0 flex items-center gap-3">
                            <img src="{{ asset('images/logoppd.png') }}" alt="PPD Logo"
                                class="w-10 h-10 object-contain">
                            <div class="leading-tight">
                                <h1 class="font-bold text-gray-900 text-lg">PPD Kluang</h1>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Appointment
                                    System</p>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:space-x-8 text-sm font-medium">
                            {{-- ===== ADMIN LINKS ===== --}}
                            @if (Auth::check() && Auth::user()->role == 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-house"></i> Dashboard
                                </a>

                                <a href="{{ route('admin.requests') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.requests') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-check"></i> Request
                                </a>

                                <a href="{{ route('admin.users') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.users') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-user"></i> Users
                                </a>

                                <a href="{{ route('admin.availability') }}"
                                    class="nav-link {{ request()->routeIs('admin.availability') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fas fa-calendar-times"></i> Availability
                                </a>

                                <a href="{{ route('admin.reports') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.reports') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-file"></i> Report
                                </a>

                                {{-- ===== USER LINKS ===== --}}
                            @else
                                <a href="{{ route('dashboard') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-house"></i> Dashboard
                                </a>

                                <a href="{{ route('appointments.create') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('appointments.create') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-calendar-plus"></i> Book Appointment
                                </a>

                                <a href="{{ route('my.appointments') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('my.appointments') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-list-check"></i> My Appointments
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">

                        <div class="relative ml-3">

                            <button type="button" onclick="toggleUserDropdown()"
                                class="flex items-center gap-3 focus:outline-none transition hover:bg-gray-50 rounded-lg p-2 group"
                                id="user-menu-button" aria-expanded="false" aria-haspopup="true">

                                <div class="text-right hidden md:block">
                                    <div
                                        class="text-sm font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition">
                                        {{ Auth::user()->name }}</div>
                                    <div
                                        class="text-[10px] uppercase {{ Auth::user()->role == 'admin' ? 'text-blue-600 bg-blue-50' : 'text-green-600 bg-green-50' }} font-bold px-2 py-0.5 rounded-full inline-block mt-0.5">
                                        {{ Auth::user()->role == 'admin' ? 'Admin' : 'User' }}
                                    </div>
                                </div>

                                <div
                                    class="h-10 w-10 rounded-full border-2 border-white shadow-sm overflow-hidden bg-gray-200 ring-2 ring-transparent group-hover:ring-blue-100 transition">
                                    <img class="h-full w-full object-cover"
                                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&bold=true"
                                        alt="{{ Auth::user()->name }}">
                                </div>

                                <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div id="user-dropdown-menu"
                                class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-lg py-2 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 transform transition-all duration-200 origin-top-right">

                                <div class="px-4 py-3 border-b border-gray-100 md:hidden">
                                    <p class="text-xs text-gray-500">Signed in as</p>
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                </div>

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

                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                    </div>

                </div>
            </div>
        </nav>

        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200 mt-auto py-6">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs text-gray-400">© {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights
                    reserved.</p>
            </div>
        </footer>

    </div>

    {{-- SCRIPTS FOR DROPDOWN INTERACTION --}}
    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown-menu');

            if (dropdown.classList.contains('hidden')) {
                // Show dropdown
                dropdown.classList.remove('hidden');
                // Animation classes
                dropdown.classList.add('opacity-100', 'scale-100');
                dropdown.classList.remove('opacity-0', 'scale-95');
            } else {
                // Hide dropdown
                dropdown.classList.add('hidden');
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
            }
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            const button = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('user-dropdown-menu');

            // Check if click was outside the button AND outside the dropdown
            if (button && !button.contains(event.target) && dropdown && !dropdown.contains(event.target) && !dropdown
                .classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
            }
        }
    </script>
</body>

</html>
