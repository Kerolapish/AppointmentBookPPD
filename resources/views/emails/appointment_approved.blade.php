<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Confirmed</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background-color: #22c55e; color: white; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; font-weight: bold; font-size: 18px; }
        .content { padding: 20px; }
        .details-table { w_idth: 100%; border-collapse: collapse; margin-top: 15px; }
        .details-table td { padding: 10px; border-bottom: 1px solid #edf2f7; }
        .details-table td.label { font-weight: bold; w_idth: 35%; color: #4a5568; }
        .footer { text-align: center; margin-top: 25px; font-size: 12px; color: #718096; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        APPOINTMENT CONFIRMED
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $appointment->user->name ?? 'User' }}</strong>,</p>
        <p>Your appointment application with PPD Kluang has been successfully reviewed and **Approved**. Below are the complete details of your booking:</p>
        
        <table class="details-table">
            <tr>
                <td class="label">Purpose / IPS:</td>
                <td>{{ $appointment->purpose }}</td>
            </tr>
            <tr>
                <td class="label">Date:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Time slot:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
            </tr>
            <tr>
                <td class="label">Contact Phone:</td>
                <td>{{ $appointment->user->phone ?? '-' }}</td>
            </tr>
        </table>

        <p style="margin-top: 20px;">Please ensure you arrive at least 10 minutes prior to your scheduled time slot. If you need any further assistance, feel free to contact us.</p>
    </div>

    <div class="footer">
        © {{ date('Y') }} PPD Kluang Appointment System. All rights reserved.
    </div>
</div>

</body>
</html>