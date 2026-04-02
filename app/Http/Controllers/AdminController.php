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
        $approvedCount = Appointment::where('status', 'confirmed')->count();
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
        // We use "like $search%" to find names STARTING with the input
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
        // We use 'clone' so the search/filter applies to ALL statuses
        $pending = (clone $query)->where('status', 'pending')->orderBy('date', 'asc')->get();
        $approved = (clone $query)->where('status', 'confirmed')->orderBy('date', 'asc')->get();
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
        // If 'month', it uses the default set above

        // 2. Apply Date Filter
        $query->whereBetween('date', [$startDate, $endDate]);

        // 3. Get Data for Cards & Charts
        $totalAppointments = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $approved = (clone $query)->where('status', 'confirmed')->count();
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
        $approved = $appointments->where('status', 'confirmed')->count();
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

        // 2. Apply Search Filter (if search text exists)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // 3. Separate the results into two groups
        // Assuming you have a 'role' column or check based on middleware logic
        // We use 'clone' so the search applies to both queries
        $admins = (clone $query)->where('role', 'admin')->orderBy('created_at', 'desc')->get();
        $users  = (clone $query)->where('role', 'user')->orderBy('created_at', 'desc')->get();

        return view('admin.users', compact('admins', 'users'));
    }

    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'approved'; 
        $appointment->save();

        // --- WHATSAPP NOTIFICATION LOGIC ---
        try {
            $sid    = env('TWILIO_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM');
            $twilio = new Client($sid, $token);

            // 1. Get the phone number and remove any accidental hidden spaces
            $userPhone = trim($appointment->user->phone);

            // 2. Force the +60 format for Malaysian numbers if it starts with '0'
            if (str_starts_with($userPhone, '0')) {
                $userPhone = '+60' . substr($userPhone, 1);
            }

            // 3. Add the WhatsApp prefix
            $to = "whatsapp:" . $userPhone;

            // --- THE DEBUG TEST ---
            // This will pause the code and print the final number on your screen.
            // (We will remove this once we know it works!)
            // dd('STOPPING TO CHECK NUMBER: ', $to);
            // ----------------------

            $messageBody = "Hello {$appointment->user->name}! Great news, your appointment for {$appointment->purpose} on " . \Carbon\Carbon::parse($appointment->date)->format('d M Y') . " has been APPROVED. See you then!";

            $twilio->messages->create($to, [
                "from" => $from,
                "body" => $messageBody
            ]);

        } catch (\Exception $e) {
            // If WhatsApp fails, we still want the approval to work, 
            // but we can log the error so you know what went wrong.
            \Log::error('WhatsApp Error: ' . $e->getMessage());
        }
        // -----------------------------------

        return redirect()->back()->with('success', 'Appointment approved and WhatsApp notification sent!');
    }

    public function reject($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'rejected';
        $appointment->save();
        return redirect()->back()->with('success', 'Appointment Rejected');
    }

    public function complaints()
    {
        // Fetch complaints with the associated user, latest first
        // Ensure you have public function user() { return $this->belongsTo(User::class); } in your Complaint model
        $complaints = \App\Models\Complaint::with('user')->latest()->paginate(10);

        return view('admin.admin_complaints', compact('complaints'));
    }

    public function resolveComplaint(Request $request, $id)
    {
        $request->validate([
            'admin_response' => 'required|string|max:1000',
        ]);

        $complaint = \App\Models\Complaint::findOrFail($id);

        $complaint->update([
            'status' => 'resolved',
            'admin_response' => $request->admin_response,
            // Optional: Capture who resolved it if you want
            // 'resolved_by' => auth()->id(), 
        ]);

        return redirect()->back()->with('success', 'Complaint marked as resolved successfully.');
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

        return redirect()->back()->with('success', 'Reschedule requested successfully.');
    }
}
