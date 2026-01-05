@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Welcome back, {{ Auth::user()->name ?? 'User' }}!</h2>
        <p class="text-gray-500 mt-1">Manage your appointments with Pejabat Pendidikan Daerah Kluang</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                    <i class="fa-solid fa-calendar-check text-xl"></i>
                </div>
                <span class="bg-green-100 text-green-600 text-xs font-semibold px-2.5 py-1 rounded-full">+12%</span>
            </div>
            <div class="mt-4">
                <h3 class="text-4xl font-bold text-gray-800">8</h3>
                <p class="text-gray-500 text-sm font-medium">Total Appointments</p>
            </div>
        </div>
        </div>

    @endsection