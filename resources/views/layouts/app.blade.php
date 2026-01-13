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
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Appointment System</p>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:space-x-8 text-sm font-medium">

                            {{-- ===== ADMIN LINKS ===== --}}
                            @if (Auth::check() && Auth::user()->role == 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-house"></i> Dashboard
                                </a>

                                <a href="{{ route('admin.appointments') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.appointments') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-check"></i> Request
                                </a>

                                <a href="{{ route('admin.users') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.users') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-user"></i> Users
                                </a>

                                <a href="{{ route('admin.users') }}"
                                    class="flex items-center gap-2 transition {{ request()->routeIs('admin.users') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
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

                    <div class="hidden sm:flex sm:items-center sm:ml-6 gap-5">
                        
                        <div class="text-right hidden md:block">
                            <div class="text-sm font-semibold text-gray-900 leading-tight">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-xs text-green-600 font-medium">
                                {{ Auth::user()->role == 'admin' ? 'Admin' : 'User' }}
                            </div>
                        </div>

                        <a href="{{ route('profile.show') }}" 
                           class="flex items-center justify-center h-9 w-9 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-600 transition border border-gray-200" 
                           title="My Profile">
                            <i class="fa-solid fa-user"></i>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                class="flex items-center justify-center h-9 w-9 rounded-full bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition border border-red-100"
                                title="Log Out">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                        </form>

                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
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
                <p class="text-xs text-gray-400">© {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.</p>
            </div>
        </footer>

    </div>
</body>

</html>