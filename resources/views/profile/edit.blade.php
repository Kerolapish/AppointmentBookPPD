@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
            <p class="text-gray-500 text-sm mt-1">Update your personal information</p>
        </div>
        <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-gray-700 font-semibold text-sm flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Profile
        </a>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>Saved successfully.</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        
        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +60123456789"
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="ips_name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">IPS Name</label>
                    <input type="text" name="ips_name" id="ips_name" value="{{ old('ips_name', $user->ips_name) }}" placeholder="Institution Name"
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('ips_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Address</label>
                    <textarea name="address" id="address" rows="3" placeholder="Enter your full address"
                              class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none">{{ old('address', $user->address) }}</textarea>
                    @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('profile.show') }}" class="px-5 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg shadow-md shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900">Update Password</h2>
            <p class="text-gray-500 text-sm">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        <form method="post" action="{{ route('password.update.profile') }}" class="space-y-6">
            @csrf
            @method('put')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="update_password_current_password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Current Password</label>
                    <input type="password" name="current_password" id="update_password_current_password" 
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('current_password', 'updatePassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="hidden md:block"></div> <div>
                    <label for="update_password_password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">New Password</label>
                    <input type="password" name="password" id="update_password_password" 
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('password', 'updatePassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="update_password_password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="update_password_password_confirmation" 
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('password_confirmation', 'updatePassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                @if (session('status') === 'password-updated')
                    <p class="text-sm text-green-600 font-medium">Saved.</p>
                @endif
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white font-bold rounded-lg shadow-md hover:bg-black transition flex items-center gap-2">
                    <i class="fa-solid fa-lock"></i> Update Password
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-red-50 rounded-xl shadow-sm border border-red-100 p-8">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-red-800">Danger Zone</h2>
            <p class="text-red-600 text-sm">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.');">
            @csrf
            @method('delete')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="delete_account_password" class="block text-xs font-bold text-red-800 uppercase tracking-wide mb-2">Confirm Password to Delete</label>
                    <input type="password" name="password" id="delete_account_password" placeholder="Password"
                           class="w-full bg-white text-gray-900 border border-red-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    @error('password', 'userDeletion') <span class="text-red-600 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-red-200 flex items-center justify-end gap-3">
                <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-bold rounded-lg shadow-md hover:bg-red-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i> Delete Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection