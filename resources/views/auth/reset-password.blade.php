@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-50 relative py-12 sm:px-6 lg:px-8">
    
    <div class="w-full sm:max-w-md mt-6 bg-white shadow-xl rounded-2xl overflow-hidden">
        
        <div class="bg-gradient-to-r from-green-600 to-teal-700 text-white text-center py-8 px-6">
            <div class="mx-auto w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-key text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold mb-1">Create New Password</h2>
            <p class="text-green-100 text-xs uppercase tracking-wider font-semibold">PPD Kluang System</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                            class="w-full border border-gray-200 bg-gray-50 rounded-lg py-2.5 pl-10 pr-4 text-gray-500 text-sm cursor-not-allowed">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required autofocus autocomplete="new-password"
                            class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition text-sm">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-check-double"></i>
                        </span>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full border border-gray-300 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-teal-700 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-xl transition transform hover:-translate-y-0.5">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection