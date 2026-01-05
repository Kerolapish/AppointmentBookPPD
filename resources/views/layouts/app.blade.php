<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PPD Kluang')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    <nav class="bg-white border-b border-gray-200 px-6 py-3 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        
        <div class="flex items-center gap-6">
            
            <div class="flex items-center gap-3">
                <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center shadow-sm border border-gray-100 overflow-hidden">
                    <img src="{{ asset('images/logoPPD.png') }}" alt="PPD Logo" class="w-full h-full object-contain">
                </div>
                <div class="hidden sm:block leading-tight">
                    <h1 class="text-blue-900 text-base font-bold tracking-tight">PPD Kluang</h1>
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Appointment System</p>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" 
               class="h-fit hidden md:flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-md text-sm font-semibold transition-colors duration-200 border border-transparent hover:border-blue-200">
                <i class="fa-solid fa-house text-xs"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('appointments') }}" 
               class="h-fit hidden md:flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-md text-sm font-semibold transition-colors duration-200 border border-transparent hover:border-blue-200">
                <i class="fa-solid fa-calendar text-xs"></i>
                <span>Book Appointment</span>
            </a>

            <a href="{{ route('dashboard') }}" 
               class="h-fit hidden md:flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-md text-sm font-semibold transition-colors duration-200 border border-transparent hover:border-blue-200">
                <i class="fa-solid fa-list text-xs"></i>
                <span>My Booking</span>
            </a>

        </div>

        <div class="flex items-center gap-5">
            <button class="relative text-gray-400 hover:text-blue-600 transition-colors">
                <i class="fa-solid fa-bell text-xl"></i>
                <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            </button>

            <div class="flex items-center gap-3 pl-5 border-l border-gray-200 cursor-pointer">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800 leading-none">{{ Auth::user()->name ?? 'Ahmad Razak' }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Guru</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold border-2 border-white shadow-sm">
                    AR
                </div>
            </div>
        </div>
    </div>
</nav>

    <main class="max-w-7xl mx-auto p-6 md:p-8">
        @yield('content')
    </main>

    <footer class="max-w-7xl mx-auto px-8 py-6 text-center text-xs text-gray-400 border-t border-gray-100 mt-12">
        &copy; 2025 Pejabat Pendidikan Daerah Kluang. All rights reserved.
    </footer>

</body>

</html>
