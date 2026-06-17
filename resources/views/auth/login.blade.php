@extends('layouts.guest')

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">

            <div
                class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 rounded-t-xl flex items-center justify-center gap-8 shadow-inner">

                <div class="flex items-center justify-center h-20">
                    <img src="{{ asset('images/logoPPD.png') }}" alt="PPD Logo"
                        class="h-20 w-auto object-contain drop-shadow-md">
                </div>

                <div class="flex items-center justify-center h-20">
                    <img src="{{ asset('images/logoKPM.PNG') }}" alt="Second Logo"
                        class="h-20 w-auto object-contain drop-shadow-md">
                </div>

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
                            <input type="email" name="email" value="{{ old('email', request()->cookie('remember_email')) }}" required autofocus
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
                            <input type="password" name="password" id="password" required
                                class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-10 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm @error('password') border-red-500 @enderror"
                                placeholder="Enter your password">

                            <button type="button" onclick="togglePassword('password', 'toggle-icon-login')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fa-regular fa-eye-slash" id="toggle-icon-login"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" {{ request()->hasCookie('remember_email') ? 'checked' : '' }}
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

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
@endsection
