@extends('layouts.app')

@section('title', 'Submit Complaint')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/complaint.css') }}">
@endpush

@section('content')
    <div class="main-container max-w-4xl mx-auto">
        <div class="header-section mb-8 text-center">
            <h1 class="text-2xl font-bold text-gray-900">Submit Your Complaint</h1>
            <p class="text-gray-500 mt-2">Your feedback is important. We will work to resolve it promptly.</p>
        </div>

        <div class="form-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="form-title text-lg font-bold text-gray-800 mb-6 pb-4 border-b">Complaint Details</div>
            
            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">IPS</label>
                        <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Enter IPS">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                        <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="">Select a purpose</option>
                            <option value="complaint">File a Complaint</option>
                            <option value="feedback">General Feedback</option>
                        </select>
                    </div>
                    <div class="form-group md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location / Details</label>
                        <textarea class="w-full border border-gray-300 rounded-lg px-4 py-2 h-32 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Please provide details..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2">
                        Submit Complaint <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection