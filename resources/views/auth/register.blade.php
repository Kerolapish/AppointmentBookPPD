@extends('layouts.guest')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        
        <div class="header-gradient text-white text-center py-8 px-6">
            <div class="mx-auto w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-user-plus text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold mb-1">Create Account</h2>
            <p class="text-blue-100 text-xs uppercase tracking-wider font-semibold">Join PPD Kluang System</p>
        </div>

        <div class="auth-body">
            
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus 
                            class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm @error('name') border-red-500 @enderror"
                            placeholder="John Doe">
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                            class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm @error('email') border-red-500 @enderror"
                            placeholder="your.email@example.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required 
                            class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm @error('password') border-red-500 @enderror"
                            placeholder="Create a password">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <input type="password" name="password_confirmation" required 
                            class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm"
                            placeholder="Repeat password">
                    </div>
                </div>

                <button type="submit" class="w-full header-gradient text-white font-bold py-3 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                    Register Account
                </button>

            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                Already have an account? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Sign in</a>
            </div>
        </div>
    </div>
    
    <div class="absolute bottom-6 w-full text-center">
        <p class="text-gray-400 text-xs">© {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.</p>
    </div>
</div>
@endsection