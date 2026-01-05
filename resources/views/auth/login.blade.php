@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
    <div class="header-gradient p-8 text-center text-white">
        <div class="mb-4 flex justify-center">
            <div class="bg-white text-blue-600 rounded-full w-16 h-16 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-calendar-check text-3xl"></i>
            </div>
        </div>
        <h1 class="text-2xl font-bold mb-1">Appointment Booking System</h1>
        <p class="text-blue-100 text-sm font-medium">Pejabat Pendidikan Daerah Kluang</p>
    </div>

    <div class="p-8">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">Email Address</label>
                <input class="w-full border border-gray-300 rounded-lg py-2.5 px-3" id="email" type="email" name="email">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">Password</label>
                <input class="w-full border border-gray-300 rounded-lg py-2.5 px-3" id="password" type="password" name="password">
            </div>

            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-blue-500/30" type="submit">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection