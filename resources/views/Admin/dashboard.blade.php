@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <div>
        <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
        <p class="text-gray-500 text-sm mt-1">Overview of appointment requests</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            
            <div class="bg-white rounded-xl p-6 border border-yellow-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600 font-bold text-lg">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Pending Request</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $pendingCount }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-green-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 font-bold text-lg">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Approved</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $approvedCount }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-red-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-bold text-lg">
                    <i class="fa-regular fa-circle-xmark"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Rejected</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                </div>
            </div>

        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Recent Requests</h3>
            <a href="{{ route('admin.appointments') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All</a>
        </div>

        <div class="space-y-4">
            @forelse($appointments as $appointment)
            
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center gap-6 hover:shadow-md transition">
                
                <div class="flex-shrink-0 w-16 h-16 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center">
                    <span class="text-xl font-bold leading-none">{{ \Carbon\Carbon::parse($appointment->date)->format('d') }}</span>
                    <span class="text-[10px] font-bold uppercase mt-1">{{ \Carbon\Carbon::parse($appointment->date)->format('M') }}</span>
                </div>

                <div class="flex-1 text-center sm:text-left">
                    <h4 class="text-md font-bold text-gray-900">{{ $appointment->purpose }}</h4>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm text-gray-500 mt-1">
                        
                        <span class="flex items-center justify-center sm:justify-start gap-1">
                            <i class="fa-regular fa-user text-gray-400"></i> 
                            {{ $appointment->user->name ?? 'Unknown User' }}
                        </span>

                        <span class="flex items-center justify-center sm:justify-start gap-1">
                            <i class="fa-regular fa-clock text-gray-400"></i> 
                            {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                        </span>
                    </div>
                </div>

                <div>
                    @if($appointment->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-4 py-2 rounded-full">
                            Pending
                        </span>
                    @elseif($appointment->status == 'confirmed')
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-4 py-2 rounded-full">
                            Approved
                        </span>
                    @elseif($appointment->status == 'rejected')
                        <span class="bg-red-100 text-red-700 text-xs font-bold px-4 py-2 rounded-full">
                            Rejected
                        </span>
                    @endif
                </div>

            </div>
            @empty
            <div class="text-center py-10 bg-white rounded-xl border border-gray-100">
                <p class="text-gray-500">No recent appointments found.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    </div>

</div>
@endsection