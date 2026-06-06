<!DOCTYPE html>
<html>
<head>
    <title>Appointment Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; rounded-lg: 8px;">
        <h2 style="color: #1f2937;">Appointment Status Notice</h2>
        <p>Hello <strong>{{ $appointment->name }}</strong>,</p>
        
        <p style="background-color: #f3f4f6; padding: 15px; border-left: 4px solid #4b5563; color: #374151; font-style: italic;">
            "{{ $messageBody }}"
        </p>
        
        <h4 style="margin-bottom: 8px; color: #4b5563;">Original Request Summary:</h4>
        <ul style="list-style-type: none; padding-left: 0;">
            <li><strong>Date:</strong> {{ $appointment->date }}</li>
            <li><strong>Time Slot:</strong> {{ $appointment->time }}</li>
            <li><strong>Purpose:</strong> {{ $appointment->purpose }}</li>
        </ul>
        
        <p style="margin-top: 25px; font-size: 12px; color: #9ca3af;">
            This is an automated notification from the Private Unit Office tracking management application system.
        </p>
    </div>
</body>
</html>