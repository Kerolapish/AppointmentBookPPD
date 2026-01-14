<!DOCTYPE html>
<html>

<head>
    <title>Analytics Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #1a202c;
        }

        .header p {
            margin: 5px 0 0;
            color: #718096;
        }

        /* Stats Grid */
        .stats-container {
            width: 100%;
            margin-bottom: 30px;
        }

        .stat-box {
            float: left;
            width: 23%;
            margin-right: 2%;
            padding: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
        }

        .stat-box:last-child {
            margin-right: 0;
        }

        .stat-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
            margin-bottom: 5px;
        }

        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Status Badges */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Green */
        .pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* Yellow */
        .rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Red */

        /* Helper to clear floats */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Analytics Report</h2>
        <p>Period: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <div class="stats-container clearfix">
        <div class="stat-box">
            <span class="stat-label">Total Requests</span>
            <span class="stat-value">{{ $totalAppointments }}</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Pending</span>
            <span class="stat-value">{{ $pending }}</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Approved</span>
            <span class="stat-value">{{ $approved }}</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Rejected</span>
            <span class="stat-value">{{ $rejected }}</span>
        </div>
    </div>

    <h3 style="margin-bottom: 15px; color: #1a202c;">Activity Log</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Applicant</th>
                <th>Contact</th>
                <th style="text-align: right;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $app)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($app->date)->format('d M Y') }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $app->name }}</div>
                        <div style="color: #718096; font-size: 10px;">ID: #{{ $app->id }}</div>
                    </td>
                    <td>
                        <div>{{ $app->email }}</div>
                        <div style="color: #718096; font-size: 10px;">{{ $app->phone ?? '-' }}</div>
                    </td>
                    <td style="text-align: right;">
                        <span class="badge {{ $app->status }}">
                            {{ ucfirst($app->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #718096;">
                        No records found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
