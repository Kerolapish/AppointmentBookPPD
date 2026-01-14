@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Complaints</h1>
                    <p class="text-gray-500 text-sm mt-1">Track the status of your reported issues</p>
                </div>
                <a href="{{ route('complaint.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Submit New Complaint
                </a>
            </div>

            @if ($complaints->count() > 0)
                <div class="grid gap-6">
                    @foreach ($complaints as $complaint)
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div class="space-y-3 flex-grow">

                                        <div class="flex items-center gap-3">
                                            @if ($complaint->status == 'resolved')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fa-solid fa-check-circle mr-1.5"></i> Resolved
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    In Progress
                                                </span>
                                            @endif

                                            <span class="text-gray-400 text-sm border-l border-gray-200 pl-3">
                                                Submitted on
                                                {{ \Carbon\Carbon::parse($complaint->created_at)->format('d M Y') }}
                                            </span>
                                        </div>

                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $complaint->purpose }}
                                            @if ($complaint->ips)
                                                <span class="text-gray-400 font-normal text-base"> •
                                                    {{ $complaint->ips }}</span>
                                            @endif
                                        </h3>

                                        <div
                                            class="p-4 bg-gray-50 rounded-lg border border-gray-100 mb-4 text-gray-700 text-sm leading-relaxed">
                                            {{ $complaint->description }}
                                        </div>

                                        @if ($complaint->attachment)
                                            <div class="mt-4 border-t border-dashed border-gray-200 pt-3">
                                                <span
                                                    class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">
                                                    Attachment
                                                </span>

                                                @php
                                                    $extension = pathinfo($complaint->attachment, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'gif',
                                                        'webp',
                                                    ]);
                                                @endphp

                                                @if ($isImage)
                                                    <a href="{{ asset('storage/' . $complaint->attachment) }}"
                                                        target="_blank"
                                                        class="group relative inline-block overflow-hidden rounded-lg border border-gray-200 w-32 h-32">
                                                        <img src="{{ asset('storage/' . $complaint->attachment) }}"
                                                            alt="Evidence"
                                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">

                                                        <div
                                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                                            <i
                                                                class="fa-solid fa-magnifying-glass text-white opacity-0 group-hover:opacity-100"></i>
                                                        </div>
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $complaint->attachment) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm font-medium transition-colors border border-gray-200">
                                                        <i class="fa-solid fa-paperclip text-gray-500"></i>
                                                        View Document ({{ strtoupper($extension) }})
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($complaint->status == 'resolved' && !empty($complaint->admin_response))
                                    <div class="mt-6 pt-5 border-t border-gray-100">
                                        <div class="flex gap-4 bg-green-50 rounded-lg p-4 border border-green-100">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-8 h-8 bg-green-200 text-green-700 rounded-full flex items-center justify-center">
                                                    <i class="fa-solid fa-user-shield text-sm"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-green-900 mb-1">Admin Response</h4>
                                                <p class="text-sm text-green-800">{{ $complaint->admin_response }}</p>
                                                <p class="text-xs text-green-600 mt-2">
                                                    Resolved on
                                                    {{ \Carbon\Carbon::parse($complaint->updated_at)->format('d M Y, h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($complaint->location)
                                <div
                                    class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex items-center text-xs text-gray-500">
                                    <i class="fa-solid fa-location-dot mr-2 text-gray-400"></i>
                                    Location: <span
                                        class="font-medium text-gray-700 ml-1">{{ $complaint->location }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{-- $complaints->links() --}}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div
                        class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-clipboard text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No complaints found</h3>
                    <p class="text-gray-500 mt-1 mb-6">You haven't submitted any complaints yet.</p>
                    <a href="{{ route('complaint.create') }}"
                        class="text-blue-600 hover:text-blue-700 font-medium text-sm hover:underline">
                        Submit your first complaint &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
