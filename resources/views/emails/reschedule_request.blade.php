<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reschedule Requested</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background-color: #eab308; color: white; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; font-weight: bold; font-size: 18px; }
        .content { padding: 20px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .details-table td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .details-table td.label { font-weight: bold; width: 35%; color: #4a5568; }
        .footer { text-align: center; margin-top: 25px; font-size: 12px; color: #718096; }
        .reason-box { background-color: #fef9c3; border-left: 4px solid #eab308; padding: 15px; margin: 20px 0; color: #854d0e; }
        .button { display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        ACTION REQUIRED: RESCHEDULE APPOINTMENT
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $appointment->user->name ?? 'User' }}</strong>,</p>
        <p>An administrator has requested that you reschedule your upcoming appointment with PPD Kluang.</p>
        
        <div class="reason-box">
            <strong>Reason/Message from Admin:</strong><br>
            {{ $appointment->reschedule_reason }}
        </div>
        
        <table class="details-table">
            <tr>
                <td class="label">Purpose / IPS:</td>
                <td>{{ $appointment->purpose }}</td>
            </tr>
            <tr>
                <td class="label">Current Date:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Current Time slot:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
            </tr>
        </table>

        <p style="margin-top: 20px;">Please click the button below to log into your dashboard and select a new date and time for this appointment.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('my.appointments') }}" class="button">Log In to Reschedule</a>
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} PPD Kluang Appointment System. All rights reserved.
    </div>
</div>

</body>
</html>
