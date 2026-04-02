@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Analytics Report</h1>
            <p class="text-sm text-gray-500 mt-1">Overview of system performance ({{ $startDate->format('d M') }} -
                {{ $endDate->format('d M Y') }}).</p>
        </div>

        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.reports') }}" id="filterForm" class="m-0">
                <div class="relative">
                    <select name="filter" onchange="document.getElementById('filterForm').submit()"
                        class="h-10 appearance-none bg-white border border-gray-300 text-gray-700 pl-4 pr-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm cursor-pointer flex items-center">
                        <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </form>

            <a href="{{ route('admin.report.pdf', ['filter' => $filter]) }}" target="_blank"
                class="h-10 bg-red-600 hover:bg-red-700 text-white flex items-center gap-2 px-4 rounded-lg text-sm font-medium shadow-sm transition-colors">
                <i class="fa-solid fa-file-pdf"></i>
                Preview Report
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $totalAppointments }}</h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $pending }}</h3>
                </div>
                <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                    <i class="fa-regular fa-clock text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Completed</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $approved }}</h3>
                </div>
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <i class="fa-solid fa-check text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejected</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $rejected }}</h3>
                </div>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Appointments Overview</h3>
            <div class="relative h-64 w-full">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Status Distribution</h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="doughnutChart"></canvas>
            </div>
            <div class="mt-4 flex justify-center gap-4 text-xs text-gray-600">
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Confirmed
                </div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-500"></span> Pending
                </div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500"></span> Rejected
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Activity Log</h3>
        </div>
        <table class="w-full text-left">
            <thead class="bg-white text-xs text-gray-500 uppercase font-semibold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Applicant</th>
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($appointments as $app)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($app->date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs uppercase">
                                    {{ substr($app->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $app->name }}</div>
                                    <div class="text-xs text-gray-400">ID: #{{ $app->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-600">{{ $app->email }}</div>
                            <div class="text-xs text-gray-400">{{ $app->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if ($app->status == 'confirmed')
                                <span
                                    class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Confirmed</span>
                            @elseif($app->status == 'rejected')
                                <span
                                    class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Rejected</span>
                            @else
                                <span
                                    class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No records found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // 1. Data passed from Laravel
        const rawAppointments = @json($appointments);
        const pendingCount = {{ $pending }};
        const approvedCount = {{ $approved }};
        const rejectedCount = {{ $rejected }};

        // 2. Process Data for Bar Chart
        const dateCounts = {};
        rawAppointments.forEach(app => {
            const date = app.date.split(' ')[0]; // Extract YYYY-MM-DD
            dateCounts[date] = (dateCounts[date] || 0) + 1;
        });

        const labels = Object.keys(dateCounts).sort();
        const dataValues = labels.map(date => dateCounts[date]);

        // 3. Render Bar Chart
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Appointments',
                    data: dataValues,
                    backgroundColor: '#3B82F6',
                    borderRadius: 5,
                    barThickness: 20,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // 4. Render Doughnut Chart
        const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Confirmed', 'Pending', 'Rejected'],
                datasets: [{
                    data: [approvedCount, pendingCount, rejectedCount],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
