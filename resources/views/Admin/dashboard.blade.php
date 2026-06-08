@extends('layouts.app')

@section('content')
    <div class="space-y-8">

        <div>
            <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
            <p class="text-gray-500 text-sm mt-1">Overview of appointment requests</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 font-bold text-lg">
                        <i class="fa-regular fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Confirmed</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $approvedCount }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600 font-bold text-lg">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Pending Request</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $pendingCount }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-bold text-lg">
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
            <div class="flex items-center justify-between mb-4 flex-col sm:flex-row gap-4">
                <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-start">
                    <h3 class="text-lg font-bold text-gray-900">Recent Requests</h3>
                    <a href="{{ route('admin.requests') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        View All
                    </a>
                </div>

                <form action="{{ route('admin.dashboard') }}" method="GET" id="statusFilterForm"
                    class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <label for="status" class="text-sm font-medium text-gray-600">Status:</label>
                    <select name="status" id="status" onchange="document.getElementById('statusFilterForm').submit();"
                        class="rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-gray-700 bg-white px-3 py-1.5 border cursor-pointer">
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>

            <div class="space-y-4">
                @forelse($appointments as $appointment)
                    @php
                        // TYPO FIXED: Properly closed the @php block using
                    @endphp
                    $loopDate = $appointment->date ? \Carbon\Carbon::parse($appointment->date) : \Carbon\Carbon::today();
                    @endphp
                    <div
                        class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center gap-6 hover:shadow-md transition">

                        <div
                            class="flex-shrink-0 w-16 h-16 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center">
                            <span class="text-xl font-bold leading-none">{{ $loopDate->format('d') }}</span>
                            <span class="text-[10px] font-bold uppercase mt-1">{{ $loopDate->format('M') }}</span>
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <h4 class="text-md font-bold text-gray-900">{{ $appointment->purpose }}</h4>
                            <div
                                class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm text-gray-500 mt-1">
                                <span class="flex items-center justify-center sm:justify-start gap-1">
                                    <i class="fa-regular fa-user text-gray-400"></i>
                                    {{ $appointment->user->name ?? 'Unknown User' }}
                                </span>
                                <span class="flex items-center justify-center sm:justify-start gap-1">
                                    <i class="fa-regular fa-clock text-gray-400"></i>
                                    {{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('h:i A') : '--:--' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            @if ($appointment->status === 'pending')
                                <span
                                    class="bg-yellow-100 text-yellow-700 text-xs font-bold px-4 py-2 rounded-full inline-block">
                                    Pending
                                </span>
                            @elseif($appointment->status === 'approved' || $appointment->status === 'confirmed')
                                <span
                                    class="bg-green-100 text-green-700 text-xs font-bold px-4 py-2 rounded-full inline-block">
                                    Confirmed
                                </span>
                            @elseif($appointment->status === 'rejected')
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-4 py-2 rounded-full inline-block">
                                    Rejected
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <p class="text-gray-500 text-sm">No appointments found matching this status.</p>
                    </div>
                @endforelse
            </div>

            @if ($appointments->hasPages())
                <div class="mt-6">
                    {{ $appointments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
