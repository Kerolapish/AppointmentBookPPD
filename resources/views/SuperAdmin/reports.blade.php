@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Advanced Analytics</h2>
        <p class="text-sm text-gray-500 mt-1">Deep dive into system performance and booking trends.</p>
    </div>
    <a href="{{ route('super_admin.appointments.export') }}" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-download"></i> Export Full Report
    </a>
</div>

{{-- Top Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Completion Rate</p>
        <h3 class="text-3xl font-black text-gray-900 mt-2">
            {{ $totalAppointments > 0 ? round(($approvedCount / $totalAppointments) * 100) : 0 }}%
        </h3>
        <p class="text-xs text-green-600 mt-2 font-medium">Based on approved slots</p>
    </div>
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Cancellation Rate</p>
        <h3 class="text-3xl font-black text-gray-900 mt-2">
            {{ $totalAppointments > 0 ? round(($cancelledCount / $totalAppointments) * 100) : 0 }}%
        </h3>
        <p class="text-xs text-red-600 mt-2 font-medium">Users losing their slots</p>
    </div>
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Total Data Points</p>
        <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $totalUsers + $totalAppointments }}</h3>
        <p class="text-xs text-blue-600 mt-2 font-medium">Combined users & bookings</p>
    </div>
</div>

{{-- Charts Section --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    {{-- Line Chart: Appointment Trends --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h4 class="font-bold text-gray-800 mb-4">Booking Trends (Last 6 Months)</h4>
        <canvas id="trendChart" height="200"></canvas>
    </div>

    {{-- Pie Chart: Status Breakdown --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h4 class="font-bold text-gray-800 mb-4">Appointment Status Distribution</h4>
        <div class="max-w-[300px] mx-auto">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

{{-- Scripts for Charts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Trend Chart
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Appointments',
                data: {!! json_encode($appointmentCounts) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4
            }]
        }
    });

    // Status Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Cancelled', 'Other'],
            datasets: [{
                data: [{{ $approvedCount }}, {{ $cancelledCount }}, {{ $totalAppointments - ($approvedCount + $cancelledCount) }}],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b']
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection