@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Super Admin Availability Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Manage office closures, public holidays, and blocked dates.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Block a New Date</h3>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('super_admin.availability.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="mode" value="single">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="w-full">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Select Date</label>
                    <input type="date" name="date" required min="{{ date('Y-m-d') }}" class="w-full border border-gray-400 rounded-lg py-2 px-3 text-sm text-gray-900 bg-white">
                </div>
                <div class="w-full">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Reason (Optional)</label>
                    <input type="text" name="reason" placeholder="e.g., Holiday" class="w-full border border-gray-400 rounded-lg py-2 px-4 text-sm text-gray-900 bg-white">
                </div>
                <div class="w-full md:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                        Block Date
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Upcoming Blocked Dates</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Reason</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($blockedDates ?? [] as $blocked)
                        @php $targetDate = $blocked->date ?? $blocked->off_date; @endphp
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ \Carbon\Carbon::parse($targetDate)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $blocked->reason ?? 'Not specified' }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('super_admin.availability.destroy', $blocked->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 px-3 py-1.5 text-sm font-medium">
                                        Unblock
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500">No blocked dates found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection