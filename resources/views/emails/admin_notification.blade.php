<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment {{ ucfirst($action) }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background-color: #3b82f6; color: white; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; font-weight: bold; font-size: 18px; }
        .header.cancelled { background-color: #ef4444; }
        .header.rescheduled { background-color: #eab308; }
        .content { padding: 20px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .details-table td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .details-table td.label { font-weight: bold; width: 35%; color: #4a5568; }
        .footer { text-align: center; margin-top: 25px; font-size: 12px; color: #718096; }
        .button { display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header {{ str_contains(strtolower($action), 'cancel') ? 'cancelled' : 'rescheduled' }}">
        APPOINTMENT {{ strtoupper($action) }}
    </div>
    
    <div class="content">
        <p>Hello Admin,</p>
        <p>The user <strong>{{ $appointment->user->name ?? 'User' }}</strong> has <strong>{{ $action }}</strong> their appointment.</p>
        
        <table class="details-table">
            <tr>
                <td class="label">Appointment ID:</td>
                <td>#{{ $appointment->id }}</td>
            </tr>
            <tr>
                <td class="label">Purpose / IPS:</td>
                <td>{{ $appointment->purpose }}</td>
            </tr>
            <tr>
                <td class="label">User Email:</td>
                <td>{{ $appointment->user->email ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">User Phone:</td>
                <td>{{ $appointment->user->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Current Date:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Current Time slot:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
            </tr>
            <tr>
                <td class="label">Status:</td>
                <td><strong style="text-transform: uppercase;">{{ $appointment->status }}</strong></td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ url('/admin/dashboard') }}" class="button">Go to Admin Dashboard</a>
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} PPD Kluang Appointment System. All rights reserved.
    </div>
</div>

</body>
</html>
