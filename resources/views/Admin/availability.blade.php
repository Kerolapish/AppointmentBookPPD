@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Admin Availability Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Manage office closures, public holidays, and blocked dates.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Block a New Date</h3>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.availability.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="w-full">
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Blocking Mode</label>
                <div class="flex gap-6 p-2.5 bg-gray-50 rounded-lg border border-gray-200 max-w-md">
                    <label class="inline-flex items-center cursor-pointer font-medium text-sm text-gray-700">
                        <input type="radio" name="mode" value="single" checked onchange="toggleDateInputs('single')"
                            class="text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2">Single Date</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer font-medium text-sm text-gray-700">
                        <input type="radio" name="mode" value="range" onchange="toggleDateInputs('range')"
                            class="text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2">Date Range</span>
                    </label>
                </div>
            </div>

            <div id="form-grid-wrapper" class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end transition-all duration-200">
                <div id="single_date_container" class="w-full">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Select Date</label>
                    <input type="date" id="off_date" name="off_date" min="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 outline-none">
                </div>

                <div id="range_date_container" class="hidden md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Start Date</label>
                        <input type="date" id="start_date" name="start_date" min="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">End Date</label>
                        <input type="date" id="end_date" name="end_date" min="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div id="reason_container" class="w-full">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Reason (Optional)</label>
                    <input type="text" name="reason" placeholder="e.g., Public Holiday"
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-4 text-sm text-gray-900 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 outline-none">
                </div>

                <div id="action_button_container" class="w-full md:col-span-1">
                    <button type="submit"
                        class="w-full px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-2 h-[42px] mt-2 md:mt-0">
                        <i class="fa-solid fa-lock"></i> Block Date
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
                        <th class="px-6 py-4 font-semibold">Day</th>
                        <th class="px-6 py-4 font-semibold">Reason</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($offDays as $blocked)
                        @php $targetDate = $blocked->off_date ?? $blocked->date; @endphp
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($targetDate)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($targetDate)->format('l') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $blocked->reason ?? 'Not specified' }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.availability.delete', $blocked->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to unblock this date?');"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 px-3 py-1.5 font-medium transition-colors">
                                        <i class="fa-solid fa-unlock mr-1"></i> Unblock
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                No blocked dates found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleDateInputs(mode) {
            const wrapper = document.getElementById('form-grid-wrapper');
            const single = document.getElementById('single_date_container');
            const range = document.getElementById('range_date_container');

            const offDate = document.getElementById('off_date');
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            if (mode === 'single') {
                single.classList.remove('hidden');
                range.classList.add('hidden');

                wrapper.classList.remove('md:grid-cols-4');
                wrapper.classList.add('md:grid-cols-3');

                offDate.required = true;
                startDate.required = false;
                endDate.required = false;
            } else {
                single.classList.add('hidden');
                range.classList.remove('hidden');

                wrapper.classList.remove('md:grid-cols-3');
                wrapper.classList.add('md:grid-cols-4');

                offDate.required = false;
                startDate.required = true;
                endDate.required = true;
            }
        }

        // Initializer parsing check hook
        document.addEventListener("DOMContentLoaded", function() {
            toggleDateInputs('single');
        });
    </script>
@endsection
