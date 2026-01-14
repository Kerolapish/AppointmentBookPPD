@extends('layouts.app') {{-- Or layouts.admin depending on your setup --}}

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Complaints Management</h1>
            <p class="text-gray-500 text-sm mt-1">Review and resolve user reported issues.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold">
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Complaint Details</th>
                    <th class="px-6 py-4">Attachment</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($complaints as $complaint)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                        {{ $complaint->created_at->format('d M Y') }}<br>
                        <span class="text-xs text-gray-400">{{ $complaint->created_at->format('h:i A') }}</span>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $complaint->user->name ?? 'Unknown User' }}</div>
                        <div class="text-xs text-gray-500">{{ $complaint->user->email ?? '-' }}</div>
                    </td>

                    <td class="px-6 py-4 max-w-xs">
                        <div class="text-sm font-semibold text-gray-800 mb-1">{{ $complaint->purpose }}</div>
                        <p class="text-xs text-gray-600 line-clamp-2" title="{{ $complaint->description }}">
                            {{Str::limit($complaint->description, 60)}}
                        </p>
                        @if($complaint->location)
                        <div class="mt-1 text-xs text-gray-400">
                            <i class="fa-solid fa-location-dot mr-1"></i> {{ $complaint->location }}
                        </div>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        @if($complaint->attachment)
                            <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-md text-xs font-medium hover:bg-blue-100 transition-colors">
                                <i class="fa-solid fa-paperclip"></i> View
                            </a>
                        @else
                            <span class="text-xs text-gray-400 italic">None</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($complaint->status == 'resolved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Resolved
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        @if($complaint->status !== 'resolved')
                            <button onclick="openResolveModal('{{ $complaint->id }}', '{{ addslashes($complaint->purpose) }}')" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm transition-all">
                                Resolve
                            </button>
                        @else
                            <button disabled class="text-gray-400 cursor-not-allowed text-xs font-medium px-3 py-1.5 border border-gray-200 rounded-lg">
                                Completed
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300 block"></i>
                        No complaints found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $complaints->links() }}
    </div>
</div>

<div id="resolveModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeResolveModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form id="resolveForm" method="POST" action="">
                    @csrf
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-check text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Resolve Complaint</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        You are resolving the complaint: <span id="modalComplaintTitle" class="font-bold text-gray-700"></span>.
                                    </p>
                                    
                                    <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-1">Admin Response / Solution</label>
                                    <textarea name="admin_response" id="admin_response" rows="4" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2"
                                        placeholder="Explain how this issue was resolved..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                            Mark as Resolved
                        </button>
                        <button type="button" onclick="closeResolveModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openResolveModal(id, title) {
        // Set the form action dynamically based on the ID
        const form = document.getElementById('resolveForm');
        form.action = `/admin/complaints/${id}/resolve`;
        
        // Set the title for context
        document.getElementById('modalComplaintTitle').innerText = title;
        
        // Show modal
        document.getElementById('resolveModal').classList.remove('hidden');
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').classList.add('hidden');
    }
</script>
@endsection