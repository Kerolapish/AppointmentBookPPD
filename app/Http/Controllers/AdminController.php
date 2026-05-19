<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // ==========================================
    // 1. DASHBOARD PAGE
    // Route: /admin/dashboard
    // ==========================================
    public function index()
    {
        // Calculate counts for the 3 colored cards
        $pendingCount = Appointment::where('status', 'pending')->count();
        // FIXED: Changed 'confirmed' to 'approved'
        $approvedCount = Appointment::where('status', 'approved')->count();
        $rejectedCount = Appointment::where('status', 'rejected')->count();

        // Get recent appointments (paginated for the list at the bottom)
        $appointments = Appointment::with('user')
            ->latest()
            ->paginate(5); // Shows 5 per page on dashboard

        return view('admin.dashboard', compact(
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'appointments'
        ));
    }

    // ==========================================
    // 2. REQUESTS PAGE
    // Route: /admin/requests
    // ==========================================
    public function appointments(Request $request)
    {
        // 1. Start a base query
        $query = Appointment::query();

        // 2. SEARCH LOGIC: Filter by User Name (First 2 chars or more)
        if ($request->filled('search')) {
            $query->where('name', 'like', $request->search . '%');
        }

        // 3. DATE FILTER LOGIC: Today, Week, Month
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('date', Carbon::now()->month)
                        ->whereYear('date', Carbon::now()->year);
                    break;
            }
        }

        // 4. Get the results based on the filtered query
        $pending = (clone $query)->where('status', 'pending')->orderBy('date', 'asc')->get();
        // FIXED: Changed 'confirmed' to 'approved'
        $approved = (clone $query)->where('status', 'approved')->orderBy('date', 'asc')->get();
        $rejected = (clone $query)->where('status', 'rejected')->orderBy('date', 'desc')->get();

        return view('admin.requests', compact('pending', 'approved', 'rejected'));
    }

    // ==========================================
    // 3. REPORTS PAGE
    // Route: /admin/reports
    // ==========================================
    public function reports(Request $request)
    {
        // 1. Determine Date Range based on Filter
        $query = Appointment::query();
        $filter = $request->get('filter', 'month'); // Default to 'month'

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if ($filter == 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($filter == 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        // 2. Apply Date Filter
        $query->whereBetween('date', [$startDate, $endDate]);

        // 3. Get Data for Cards & Charts
        $totalAppointments = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        // FIXED: Changed 'confirmed' to 'approved'
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();

        // Get Table Data
        $appointments = (clone $query)->orderBy('date', 'desc')->get();

        return view('admin.reports', compact('totalAppointments', 'pending', 'approved', 'rejected', 'appointments', 'filter', 'startDate', 'endDate'));
    }

    public function downloadReportPdf(Request $request)
    {
        $filter = $request->query('filter', 'month');
        $query = Appointment::query();
        $now = Carbon::now();

        // 1. Apply Filter
        if ($filter == 'week') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } elseif ($filter == 'year') {
            $startDate = $now->copy()->startOfYear();
            $endDate = $now->copy()->endOfYear();
        } else {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
        }

        $appointments = $query->whereBetween('date', [$startDate, $endDate])->get();

        // 2. Count Stats
        $totalAppointments = $appointments->count();
        $pending = $appointments->where('status', 'pending')->count();
        // FIXED: Changed 'confirmed' to 'approved'
        $approved = $appointments->where('status', 'approved')->count();
        $rejected = $appointments->where('status', 'rejected')->count();

        $data = compact(
            'totalAppointments',
            'pending',
            'approved',
            'rejected',
            'appointments',
            'filter',
            'startDate',
            'endDate'
        );

        // 3. Load PDF
        $pdf = Pdf::loadView('admin.report_pdf', $data);
        return $pdf->stream('report.pdf');
    }

    public function users(Request $request)
    {
        // 1. Start with a base query
        $query = User::query();

        // 2. Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // 3. Separate the results into two groups
        $admins = (clone $query)->where('role', 'admin')->orderBy('created_at', 'desc')->get();
        $users  = (clone $query)->where('role', 'user')->orderBy('created_at', 'desc')->get();

        return view('admin.users', compact('admins', 'users'));
    }

    // ==========================================
    // ACTION METHODS (Approve, Reject, Reschedule)
    // ==========================================

    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);

        // UPDATED: Save the ID of the admin who clicked approve
        $appointment->status = 'approved';
        $appointment->approved_by = auth()->id();
        $appointment->save();

        // Custom message for Approval
        $dateFormatted = \Carbon\Carbon::parse($appointment->date)->format('d M Y');
        $message = "Hello {$appointment->user->name}! Great news, your appointment for {$appointment->purpose} on {$dateFormatted} has been APPROVED. See you then!";

        $this->sendWhatsAppNotification($appointment->user->phone, $message);

        return redirect()->back()->with('success', 'Appointment approved and WhatsApp notification sent!');
    }

    public function reject(Request $request, $id)
    {
        // 1. Find the appointment
        $appointment = Appointment::findOrFail($id);

        // 2. Capture the rejection reason from the Modal
        $reason = $request->input('reason');
        if ($reason === 'Other') {
            $reason = $request->input('other_reason');
        }

        // 3. Update the database
        $appointment->status = 'rejected';
        $appointment->reject_reason = $reason; // Ensure this column exists in your migration
        $appointment->save();

        // 4. Prepare the WhatsApp Message
        $dateFormatted = Carbon::parse($appointment->date)->format('d M Y');
        $name = $appointment->user->name ?? 'Customer';

        $message = "Hello *{$name}*.\n\n" .
            "We regret to inform you that your appointment for *{$appointment->purpose}* on *{$dateFormatted}* has been *REJECTED*.\n\n" .
            "*Reason:* {$reason}\n\n" .
            "Please contact us if you have any further questions.";

        // 5. Send Notification with Error Handling
        try {
            $this->sendWhatsAppNotification($appointment->user->phone, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp Rejection Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Appointment rejected and notification sent!');
    }

    public function requestReschedule(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => 'reschedule_requested',
            'reschedule_reason' => $request->reason
        ]);

        // Custom message for Reschedule Request
        $message = "Hello {$appointment->user->name}. We need to RESCHEDULE your appointment for {$appointment->purpose}. Reason: {$request->reason}. Please log into the PPD Kluang Appointment System to select a new date.";

        $this->sendWhatsAppNotification($appointment->user->phone, $message);

        return redirect()->back()->with('success', 'Reschedule requested and WhatsApp notification sent!');
    }


    // ==========================================
    // PRIVATE HELPER METHOD FOR WHATSAPP
    // ==========================================

    private function sendWhatsAppNotification($phone, $messageBody)
    {
        try {
            $sid    = env('TWILIO_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM');
            $twilio = new Client($sid, $token);

            $userPhone = trim($phone);

            // Force the +60 format for Malaysian numbers
            if (str_starts_with($userPhone, '0')) {
                $userPhone = '+60' . substr($userPhone, 1);
            }

            $to = "whatsapp:" . $userPhone;

            $twilio->messages->create($to, [
                "from" => $from,
                "body" => $messageBody
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp Error: ' . $e->getMessage());
        }
    }

    public function getBookedTimes(Request $request)
    {
        $date = $request->query('date');

        // Get all times already booked for this date
        // Adjust 'time' to match your column name in the database
        $bookedTimes = Appointment::where('date', $date)
            ->whereIn('status', ['pending', 'approved']) // Don't block if 'rejected' or 'cancelled'
            ->pluck('time')
            ->toArray();

        return response()->json($bookedTimes);
    }
}
