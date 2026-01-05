<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Appointment Booking System PPD Kluang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Matching the gradient from the login page image */
        .header-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%);
        }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden my-8">
        
        <div class="header-gradient p-8 text-center text-white">
            <div class="mb-4 flex justify-center">
                <div class="bg-white rounded-full w-24 h-24 flex items-center justify-center shadow-md p-4 mb-2">
                    <img src="{{ asset('images/logoPPD.png') }}" alt="PPD Kluang Logo" class="w-full h-full object-contain">
                </div>
            </div>
            <h1 class="text-2xl font-bold mb-1">Appointment Booking System</h1>
            <p class="text-blue-100 text-sm font-medium">Pejabat Pendidikan Daerah Kluang</p>
        </div>

        <div class="p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Create an Account</h2>
                <p class="text-gray-500 text-sm mt-1">Sign up to book appointments</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="name">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 @error('name') border-red-500 @enderror" 
                               id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your full name">
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 @error('email') border-red-500 @enderror" 
                               id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="your.email@example.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400 @error('password') border-red-500 @enderror" 
                               id="password" type="password" name="password" required placeholder="Create a password">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="password_confirmation">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-400" 
                               id="password_confirmation" type="password" name="password_confirmation" required placeholder="Re-enter password">
                    </div>
                </div>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-200 shadow-lg shadow-blue-500/30" type="submit">
                    Register
                </button>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">Already have an account? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Login here</a></p>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center text-gray-500 text-xs mb-8">
        &copy; {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.
    </div>

</body>
</html>