@extends('layouts.app')

@section('title', 'My Bookings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/myBooking.css') }}">
@endpush

@section('content')
    <div class="main-container">
        <div class="header-section mb-6">
            <h1 class="text-2xl font-bold text-gray-800">My Bookings</h1>
            <p class="text-gray-500">Manage and track all your appointments</p>
        </div>

        <div class="tabs flex gap-4 mb-6 border-b border-gray-200">
            <div class="tab active pb-2 border-b-2 border-blue-600 font-semibold text-blue-600 cursor-pointer">Upcoming Bookings</div>
            <div class="tab pb-2 text-gray-500 cursor-pointer hover:text-gray-700">Past Bookings</div>
        </div>

        <div class="filter-bar flex flex-wrap justify-between gap-4 mb-6">
            <div class="search-container flex items-center bg-white border border-gray-300 rounded-lg px-3 py-2 w-full md:w-auto">
                <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                <input type="text" class="search-input outline-none text-sm w-64" placeholder="Search Appointments...">
            </div>
            <div class="filter-actions flex gap-3">
                <button class="btn btn-white bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-filter mr-2"></i> Filter
                </button>
                <button class="btn btn-blue bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fa-solid fa-plus mr-2"></i> New Booking
                </button>
            </div>
        </div>

        <div class="booking-list space-y-4">
            <div class="booking-card bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div class="card-content">
                    <div class="card-title font-bold text-gray-800">Consultation - Unit Pendidikan Khas</div>
                    <p class="text-sm text-gray-500">15 Jan 2024 • 10:00 AM</p>
                </div>
                <div class="card-actions flex gap-2">
                    <button class="p-2 text-gray-400 hover:text-blue-600"><i class="fa-solid fa-eye"></i></button>
                    <button class="p-2 text-gray-400 hover:text-yellow-600"><i class="fa-solid fa-pen"></i></button>
                    <button class="p-2 text-gray-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
            
            <div class="booking-card bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div class="card-content">
                    <div class="card-title font-bold text-gray-800">Document Submission</div>
                    <p class="text-sm text-gray-500">20 Jan 2024 • 2:00 PM</p>
                </div>
                <div class="card-actions flex gap-2">
                    <button class="p-2 text-gray-400 hover:text-blue-600"><i class="fa-solid fa-eye"></i></button>
                    <button class="p-2 text-gray-400 hover:text-yellow-600"><i class="fa-solid fa-pen"></i></button>
                    <button class="p-2 text-gray-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection