@extends('layouts.app') {{-- Change this to match your admin master layout wrapper --}}

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Active Approved Appointments</h1>
            <p class="text-sm text-gray-500">Manage current ongoing schedules and close them when finished.</p>
        </div>
        
        <div class="w-full md:w-80">
            <input type="text" id="liveSearchInput" placeholder="Type to search automatically..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-700">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">User/Student</th>
                    <th class="px-6 py-3">Purpose</th>
                    <th class="px-6 py-3">Location</th>
                    <th class="px-6 py-3">Date & Time</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="appointmentsTableBody">
                @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $appointment->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $appointment->purpose }}</td>
                        <td class="px-6 py-4">{{ $appointment->location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block font-medium">{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</span>
                            <span class="text-xs text-gray-500">{{ $appointment->time }}</span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <form action="{{ route('admin.appointments.complete', $appointment->id) }}" method="POST" onsubmit="return confirm('Confirm closing this appointment?');" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm transition">
                                    <i class="fa-solid fa-check-double"></i> Complete & Close
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">No active appointments found matching that parameters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $appointments->appends(request()->input())->links() }}
    </div>
</div>

<script>
    let searchInput = document.getElementById('liveSearchInput');
    let timer = null;

    searchInput.addEventListener('input', function() {
        // Clear previous timeout handler to avoid rapid API flooding
        clearTimeout(timer);

        // Wait 400ms after user stops typing to execute the auto-search filter request
        timer = setTimeout(() => {
            let searchValue = searchInput.value;
            let currentUrl = new URL(window.location.href);
            
            // Set search parameter or clear it if empty
            if(searchValue.trim() !== "") {
                currentUrl.searchParams.set('search', searchValue);
            } else {
                currentUrl.searchParams.delete('search');
            }
            
            // Reload window context with filtered dataset seamlessly
            window.location.href = currentUrl.toString();
        }, 400); 
    });
</script>
@endsection