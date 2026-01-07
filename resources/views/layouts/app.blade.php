<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPD Kluang - Appointment System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8F9FD; }
        .nav-active { color: #2563eb; font-weight: 600; }
    </style>
</head>
<body class="antialiased text-gray-800">

    <div class="min-h-screen flex flex-col">
        
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    
                    <div class="flex items-center gap-8">
                        <div class="flex-shrink-0 flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div class="leading-tight">
                                <h1 class="font-bold text-gray-900 text-lg">PPD Kluang</h1>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Appointment System</p>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:space-x-6 text-sm">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:text-blue-600 transition {{ request()->routeIs('dashboard') ? 'nav-active' : 'text-gray-500' }}">
                                <i class="fa-solid fa-house"></i> Dashboard
                            </a>
                            <a href="{{ route('appointments.create') }}" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition">
                                <i class="fa-solid fa-calendar-plus"></i> Book Appointment
                            </a>
                            <a href="my-appointments" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition">
                                <i class="fa-solid fa-list-check"></i> My Appointments
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <button class="relative text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-regular fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                        </button>

                        <div class="flex items-center gap-3 border-l pl-6 border-gray-200">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-green-600 font-semibold">User</p>
                            </div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold hover:opacity-90 transition shadow-sm" title="Click to Logout">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </button>
                            </form>
                        </div>
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