@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-50 relative py-12 sm:px-6 lg:px-8">
    
    <div class="w-full sm:max-w-md mt-6 bg-white shadow-xl rounded-2xl overflow-hidden">
        
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-center py-8 px-6">
            <div class="mx-auto w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-unlock-keyhole text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold mb-1">Forgot Password</h2>
            <p class="text-blue-100 text-xs uppercase tracking-wider font-semibold">PPD Kluang System</p>
        </div>

        <div class="p-8">
            <div class="mb-4 text-sm text-gray-600">
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">
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

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 font-bold hover:underline">
                        Back to Login
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:shadow-xl transition transform hover:-translate-y-0.5">
                        Email Reset Link
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    <div class="absolute bottom-6 w-full text-center">
        <p class="text-gray-400 text-xs">© {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.</p>
    </div>
</div>
@endsection