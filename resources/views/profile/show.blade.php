@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-500 text-sm mt-1">Manage your personal information and account settings</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>Profile details updated successfully.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                
                <div class="relative inline-block mb-4">
                    <div class="w-24 h-24 bg-emerald-500 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                <p class="text-sm text-gray-500 font-medium mb-6 uppercase">{{ Auth::user()->role }}</p>

                <a href="{{ route('profile.edit') }}" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg transition shadow-md shadow-blue-200">
                    <i class="fa-solid fa-pen mr-2"></i> Edit Profile
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Personal Information</h3>
                        <p class="text-gray-500 text-sm">Update your personal details and contact information</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <input type="text" value="{{ Auth::user()->name }}" readonly 
                                   class="w-full bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-2.5 pl-10 text-sm cursor-default focus:ring-0 focus:border-gray-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <input type="text" value="{{ Auth::user()->email }}" readonly 
                                   class="w-full bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-2.5 pl-10 text-sm cursor-default focus:ring-0 focus:border-gray-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <input type="text" value="{{ Auth::user()->phone ?? 'Not provided' }}" readonly 
                                   class="w-full bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-2.5 pl-10 text-sm cursor-default focus:ring-0 focus:border-gray-200">
                        </div>
                    </div>

                    @if (Auth::user()->role === 'user')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Institusi Pendidikan Swasta (IPS)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-school"></i>
                                </div>
                                <input type="text" value="{{ Auth::user()->ips_name ?? 'Not provided' }}" readonly 
                                       class="w-full bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-2.5 pl-10 text-sm cursor-default focus:ring-0 focus:border-gray-200">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Address</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <textarea readonly rows="2"
                                          class="w-full bg-gray-50 text-gray-700 border border-gray-200 rounded-lg py-2.5 pl-10 text-sm cursor-default focus:ring-0 focus:border-gray-200 resize-none">{{ Auth::user()->address ?? 'No address provided' }}</textarea>
                            </div>
                        </div>
                    @endif
                </div>
            </div>



        </div>
    </div>
</div>
@endsection