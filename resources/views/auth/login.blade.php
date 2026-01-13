@extends('layouts.guest')

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="header-gradient text-white text-center py-8 px-6">
                <div
                    class="mx-auto w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4">
                    <img src="{{ asset('images/logoppd.png') }}" alt="PPD Logo" class="w-10 h-10 object-contain">
                </div>
                <h2 class="text-xl font-bold mb-1">Appointment Booking System</h2>
                <p class="text-blue-100 text-xs uppercase tracking-wider font-semibold">Pejabat Pendidikan Daerah Kluang</p>
            </div>

            <div class="auth-body">

                <h3 class="text-xl font-bold text-gray-800 mb-1">Welcome Back</h3>
                <p class="text-gray-500 text-sm mb-6">Please login to your account</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm @error('email') border-red-500 @enderror"
                                placeholder="your.email@example.com">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" required
                                class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-10 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm @error('password') border-red-500 @enderror"
                                placeholder="Enter your password">

                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer">
                                <i class="fa-regular fa-eye"></i>
                            </span>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mr-2">
                            Remember me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Forgot Password?</a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full header-gradient text-white font-bold py-3 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                        Sign In
                    </button>

                </form>

                <div class="mt-6 text-center text-sm text-gray-600">
                    Don't have an account? <a href="{{ route('register') }}"
                        class="text-blue-600 font-bold hover:underline">Register here</a>
                </div>
            </div>
        </div>

        <div class="absolute bottom-6 w-full text-center">
            <p class="text-gray-400 text-xs">© {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.
            </p>
        </div>
    </div>
@endsection
