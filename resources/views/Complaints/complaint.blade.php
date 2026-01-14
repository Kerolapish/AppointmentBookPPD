@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Whoops! Something went wrong.</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-8 py-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <i class="fa-solid fa-file-pen text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Submit a Complaint</h1>
                            <p class="text-blue-100 text-sm mt-1">Please provide details about the incident. We value your
                                feedback.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf

                    @if (session('success'))
                        <div
                            class="mb-8 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-start gap-3 animate-fade-in-down">
                            <i class="fa-solid fa-circle-check mt-1"></i>
                            <div>
                                <h4 class="font-bold">Complaint Submitted!</h4>
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mb-10">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Reporter Information
                        </h3>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-6 rounded-xl border border-gray-100">
                            <div class="relative group">
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5 ml-1">Full
                                    Name</label>
                                <div
                                    class="flex items-center px-4 py-3 bg-gray-100 border border-gray-200 text-gray-500 rounded-lg cursor-not-allowed">
                                    <i class="fa-regular fa-user mr-3 text-gray-400"></i>
                                    <span class="font-medium">{{ Auth::user()->name }}</span>
                                </div>
                            </div>

                            <div class="relative group">
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5 ml-1">Email
                                    Address</label>
                                <div
                                    class="flex items-center px-4 py-3 bg-gray-100 border border-gray-200 text-gray-500 rounded-lg cursor-not-allowed">
                                    <i class="fa-regular fa-envelope mr-3 text-gray-400"></i>
                                    <span class="font-medium">{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-10">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Incident Details
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Category <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-layer-group absolute left-4 top-3.5 text-gray-400"></i>
                                    <select name="category" required
                                        class="w-full pl-11 pr-4 py-3 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-shadow bg-white text-gray-700 appearance-none">
                                        <option value="" disabled selected>Select a category</option>
                                        <option value="Facility">Facility / Maintenance</option>
                                        <option value="Staff">Staff / Service</option>
                                        <option value="System">System / Technical</option>
                                        <option value="Safety">Safety / Security</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <i
                                        class="fa-solid fa-chevron-down absolute right-4 top-4 text-xs text-gray-400 pointer-events-none"></i>
                                </div>
                                @error('category')
                                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Date of Incident <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fa-regular fa-calendar absolute left-4 top-3.5 text-gray-400"></i>
                                    <input type="date" name="incident_date" required max="{{ date('Y-m-d') }}"
                                        class="w-full pl-11 pr-4 py-3 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-shadow text-gray-700">
                                </div>
                                @error('incident_date')
                                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Location <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-location-dot absolute left-4 top-3.5 text-gray-400"></i>
                                    <input type="text" name="location" required placeholder="e.g. Main Hall, Level 2"
                                        class="w-full pl-11 pr-4 py-3 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-shadow placeholder-gray-400">
                                </div>
                                @error('location')
                                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">IPS / Institute
                                    (Optional)</label>
                                <div class="relative">
                                    <i class="fa-regular fa-building absolute left-4 top-3.5 text-gray-400"></i>
                                    <input type="text" name="ips" placeholder="e.g. SK Taman Kluang"
                                        class="w-full pl-11 pr-4 py-3 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-shadow placeholder-gray-400">
                                </div>
                                @error('ips')
                                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Description <span
                                    class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" required maxlength="1000"
                                class="w-full p-4 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 transition-shadow placeholder-gray-400 resize-none"
                                placeholder="Please describe what happened in detail..."></textarea>
                            <div class="flex justify-between mt-1 ml-1">
                                @error('description')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @else
                                    <span></span>
                                @enderror
                                <p class="text-xs text-gray-400">Max 1000 characters</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span> Evidence (Optional)
                        </h3>

                        <label class="relative block group cursor-pointer">
                            <input type="file" name="attachment" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                onchange="previewFile(this)">

                            <div
                                class="border-2 border-dashed border-gray-300 rounded-xl p-8 transition-all duration-200 group-hover:border-blue-400 group-hover:bg-blue-50/50 flex flex-col items-center justify-center text-center">

                                <div
                                    class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-white group-hover:shadow-sm transition-all">
                                    <i
                                        class="fa-solid fa-cloud-arrow-up text-xl text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                </div>

                                <div class="text-sm text-gray-600">
                                    <span class="font-bold text-blue-600 hover:underline">Click to upload</span> or drag
                                    and drop
                                </div>
                                <p class="text-xs text-gray-400 mt-2">PNG, JPG or PDF (Max 5MB)</p>

                                <div id="file-preview"
                                    class="hidden mt-4 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full items-center gap-2">
                                    <i class="fa-solid fa-paperclip"></i>
                                    <span id="file-name">filename.jpg</span>
                                </div>
                            </div>
                        </label>
                        @error('attachment')
                            <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                        <a href="{{ route('dashboard') }}"
                            class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-lg shadow-blue-200 hover:shadow-blue-300 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            Submit Complaint
                        </button>
                    </div>

                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-8">
                &copy; {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.
            </p>

        </div>
    </div>

    <script>
        function previewFile(input) {
            const preview = document.getElementById('file-preview');
            const nameSpan = document.getElementById('file-name');

            if (input.files && input.files[0]) {
                nameSpan.textContent = input.files[0].name;
                preview.classList.remove('hidden');
                preview.classList.add('inline-flex');
            } else {
                preview.classList.add('hidden');
                preview.classList.remove('inline-flex');
            }
        }
    </script>
@endsection
