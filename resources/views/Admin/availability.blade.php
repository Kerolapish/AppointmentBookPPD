@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">

                <div class="bg-gray-50 border-b border-gray-200 px-8 py-5 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-xl text-gray-800 flex items-center gap-2">
                            <i class="fas fa-calendar-times text-red-500"></i> {{ __('Manage Blocked Dates') }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Block specific dates to prevent users from booking
                            appointments.</p>
                    </div>
                </div>

                <div class="p-8">

                    <div class="bg-blue-50 rounded-lg p-6 mb-10 border border-blue-100">
                        <h3 class="font-semibold text-blue-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Add New Date
                        </h3>

                        <form action="{{ route('admin.availability.store') }}" method="POST"
                            class="flex flex-col md:flex-row gap-4 items-end">
                            @csrf

                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="start_date"
                                    class="w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    required>
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">End Date <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="end_date"
                                    class="w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    required>
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Reason (Optional)</label>
                                <input type="text" name="reason" placeholder="e.g. Public Holiday"
                                    class="w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>

                            <div>
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-sm transition flex items-center gap-2">
                                    <i class="fas fa-lock"></i> Block
                                </button>
                            </div>
                        </form>
                        <p class="text-xs text-gray-500 mt-3"><i class="fas fa-info-circle"></i> <strong>Tip:</strong> To
                            block a single day, simply select the same date for both Start and End.</p>
                    </div>

                    <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-2">Currently Blocked Dates</h3>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                                    <th class="px-6 py-4 font-semibold">Date</th>
                                    <th class="px-6 py-4 font-semibold">Reason</th>
                                    <th class="px-6 py-4 font-semibold text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($offDays as $day)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium flex items-center gap-3">
                                            <div class="bg-red-100 text-red-600 p-2 rounded">
                                                <i class="far fa-calendar-alt"></i>
                                            </div>
                                            {{ \Carbon\Carbon::parse($day->off_date)->format('d F Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                            @if ($day->reason)
                                                <span
                                                    class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm border border-gray-200">
                                                    {{ $day->reason }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 italic">No reason provided</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <form action="{{ route('admin.availability.delete', $day->id) }}" method="POST"
                                                class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="delete-btn bg-white border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-300 py-1.5 px-4 rounded shadow-sm transition inline-flex items-center gap-2 text-sm font-medium">
                                                    <i class="fas fa-unlock"></i> Unblock
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-calendar-check text-4xl text-gray-300 mb-3 block"></i>
                                            <p class="text-lg font-medium text-gray-600">No dates are currently blocked.</p>
                                            <p class="text-sm">Your booking calendar is fully open to users.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find all the unblock buttons on the page
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Find the specific form this button belongs to
                    const form = this.closest('.delete-form');

                    // Trigger the beautiful SweetAlert popup
                    Swal.fire({
                        title: 'Unblock this date?',
                        text: "Users will be able to book appointments on this day again.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981', // Tailwind Emerald 500
                        cancelButtonColor: '#d1d5db', // Tailwind Gray 300
                        confirmButtonText: 'Yes, unblock it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true // Puts the primary action on the right
                    }).then((result) => {
                        // If the user clicks "Yes", submit the form
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
