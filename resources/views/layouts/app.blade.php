<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PPD Kluang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-gray-50 font-sans">

    <nav class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between sticky top-0 z-10">
        <div class="flex items-center space-x-12">
            <div class="flex items-center">
                <div class="bg-blue-600 text-white p-2 rounded-lg mr-3">
                    <i class="fa-solid fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 text-lg font-bold leading-tight">PPD Kluang</h1>
                    <p class="text-gray-500 text-xs font-medium">Appointment System</p>
                </div>
            </div>
            <div class="hidden md:flex space-x-6 text-sm font-medium text-gray-500">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : 'hover:text-blue-600' }} flex items-center space-x-2 px-3 py-2 rounded-md transition-colors">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
                </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3 cursor-pointer">
                <div class="hidden md:block text-right">
                    <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name ?? 'Guest' }}</p>
                    <p class="text-xs text-gray-500">User</p>
                </div>
                <img class="h-9 w-9 rounded-full border-2 border-blue-100" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=2563eb&color=fff" alt="Profile">
            </div>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-6 px-8 mt-auto">
        <div class="max-w-7xl mx-auto text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.
        </div>
    </footer>

</body>
</html>