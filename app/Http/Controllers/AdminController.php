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
    public function index(Request $request)
    {
        // 1. Calculate constant metric counts for the top dashboard view cards
        $pendingCount  = Appointment::where('status', 'pending')->count();
        $approvedCount = Appointment::where('status', 'approved')->count();
        $rejectedCount = Appointment::where('status', 'rejected')->count();

        // 2. Strict white-listed fallback structure: default to 'pending' if value is missing or modified
        $statusFilter = $request->input('status');
        if (!in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
            $statusFilter = 'pending';
        }

        // 3. Pull matching paginated data bundles
        $appointments = Appointment::with('user')
            ->where('status', $statusFilter)
            ->latest()
            ->paginate(5);

        // 4. Bind the missing variable path context to block any future 500 error risks
        $appointmentDate = $request->input('date', \Carbon\Carbon::today()->format('Y-m-d'));

        return view('Admin.dashboard', compact(
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'appointments',
            'statusFilter',
            'appointmentDate'
        ));
    }

    // ==========================================
    // 2. REQUESTS PAGE
    // Route: /admin/requests
    // ==========================================
    public function appointments(Request $request)
    {
        $query = Appointment::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', $request->search . '%');
        }

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

        $pending = (clone $query)->where('status', 'pending')->orderBy('date', 'asc')->get();
        $approved = (clone $query)->where('status', 'approved')->orderBy('date', 'asc')->get();
        $rejected = (clone $query)->where('status', 'rejected')->orderBy('date', 'desc')->get();

        // FIXED: Capitalized "Admin" to match your Linux server directory
        return view('Admin.requests', compact('pending', 'approved', 'rejected'));
    }

    // ==========================================
    // 3. REPORTS PAGE
    // Route: /admin/reports
    // ==========================================
    public function reports(Request $request)
    {
        $query = Appointment::query();
        $filter = $request->get('filter', 'month');

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if ($filter == 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($filter == 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        $query->whereBetween('date', [$startDate, $endDate]);

        $totalAppointments = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();

        $appointments = (clone $query)->orderBy('date', 'desc')->get();

        // FIXED: Capitalized "Admin" to match your Linux server directory
        return view('Admin.reports', compact('totalAppointments', 'pending', 'approved', 'rejected', 'appointments', 'filter', 'startDate', 'endDate'));
    }

    public function downloadReportPdf(Request $request)
    {
        $filter = $request->query('filter', 'month');
        $query = Appointment::query();
        $now = Carbon::now();

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

        $totalAppointments = $appointments->count();
        $pending = $appointments->where('status', 'pending')->count();
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

        // FIXED: Capitalized "Admin" to match your Linux server directory
        $pdf = Pdf::loadView('Admin.report_pdf', $data);
        return $pdf->stream('report.pdf');
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $admins = (clone $query)->where('role', 'admin')->orderBy('created_at', 'desc')->get();
        $users  = (clone $query)->where('role', 'user')->orderBy('created_at', 'desc')->get();

        // FIXED: Capitalized "Admin" to match your Linux server directory
        return view('Admin.users', compact('admins', 'users'));
    }

    // ==========================================
    // ACTION METHODS (Approve, Reject, Reschedule)
    // ==========================================

    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);

        // FIXED: Changed 'handled_by' to 'approved_by' to match your database
        $appointment->status = 'approved';
        $appointment->approved_by = auth()->id();
        $appointment->save();

        // FIXED: Added safety fallback just in case the user data is missing
        $name = $appointment->user->name ?? 'Customer';
        $phone = $appointment->user->phone ?? null;

        $dateFormatted = \Carbon\Carbon::parse($appointment->date)->format('d M Y');
        $message = "Hello {$name}! Great news, your appointment for {$appointment->purpose} on {$dateFormatted} has been APPROVED. See you then!";

        // Only send WhatsApp if a phone number exists
        //if ($phone) {
        //   $this->sendWhatsAppNotification($phone, $message);
        //}

        return redirect()->back()->with('success', 'Appointment approved!');
    }
    public function reject(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $reason = $request->input('reason');
        if ($reason === 'Other') {
            $reason = $request->input('other_reason');
        }

        $appointment->status = 'rejected';
        $appointment->reject_reason = $reason;
        $appointment->save();

        $dateFormatted = Carbon::parse($appointment->date)->format('d M Y');
        $name = $appointment->user->name ?? 'Customer';

        $message = "Hello *{$name}*.\n\n" .
            "We regret to inform you that your appointment for *{$appointment->purpose}* on *{$dateFormatted}* has been *REJECTED*.\n\n" .
            "*Reason:* {$reason}\n\n" .
            "Please contact us if you have any further questions.";

        // try {
        //    $this->sendWhatsAppNotification($appointment->user->phone, $message);
        // } catch (\Exception $e) {
        //     Log::error('WhatsApp Rejection Error: ' . $e->getMessage());
        // }

        return redirect()->back()->with('success', 'Appointment rejected successfully!');
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

        $message = "Hello {$appointment->user->name}. We need to RESCHEDULE your appointment for {$appointment->purpose}. Reason: {$request->reason}. Please log into the PPD Kluang Appointment System to select a new date.";

        // $this->sendWhatsAppNotification($appointment->user->phone, $message);

        return redirect()->back()->with('success', 'Reschedule requested successfully!');
    }

    // ==========================================
    // PRIVATE HELPER METHOD FOR WHATSAPP
    // ==========================================

    private function sendWhatsAppNotification($phone, $messageBody)
    {
        // 🚨 TEMPORARILY DISABLED FOR TOMORROW'S DEMO/TESTING
        Log::info('WhatsApp notification skipped intentionally (Temporarily Commented Out)');
        return;

        /* if (!$phone) {
            Log::warning('WhatsApp skipped: user has no recorded phone number.');
            return;
        }

        try {
            $sid    = env('TWILIO_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM');
            $twilio = new Client($sid, $token);

            $userPhone = trim($phone);

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
        */
    }

    public function getBookedTimes(Request $request)
    {
        $date = $request->query('date');

        $bookedTimes = Appointment::where('date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('time')
            ->toArray();

        return response()->json($bookedTimes);
    }
}
