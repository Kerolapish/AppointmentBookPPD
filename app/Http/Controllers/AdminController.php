<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ==========================================
    // 1. DASHBOARD (Stats & Recent Activity)
    // ==========================================
    public function index()
    {
        // 1. Stats for the Cards
        // Ensure these status strings match what is in your database!
        $pendingCount = Appointment::where('status', 'pending')->count();
        $approvedCount = Appointment::where('status', 'confirmed')->count(); // You used 'confirmed' here
        $rejectedCount = Appointment::where('status', 'rejected')->count();

        // 2. The Dashboard Table (Latest activity)
        $appointments = Appointment::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('admin.dashboard', compact(
            'appointments',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    // ==========================================
    // 2. REQUESTS PAGE (Table View)
    // ==========================================
    public function appointments()
    {
        // 1. Pending Requests (Ordered by upcoming date)
        $pendingRequests = Appointment::with('user')
            ->where('status', 'pending')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        // 2. Approved Requests (Ordered by latest first)
        $approvedRequests = Appointment::with('user')
            ->where('status', 'confirmed') // Matches your 'approve' logic below
            ->orderBy('date', 'desc')
            ->get();

        // 3. Rejected Requests (Ordered by latest first)
        $rejectedRequests = Appointment::with('user')
            ->where('status', 'rejected')
            ->orderBy('date', 'desc')
            ->get();

        // Return the 'admin.requests' view we created earlier
        return view('admin.requests', compact('pendingRequests', 'approvedRequests', 'rejectedRequests'));
    }

    // ==========================================
    // 3. USER LIST PAGE
    // ==========================================
    public function users()
    {
        // Fetch users (excluding admins to keep the list clean)
        $users = User::where('usertype', '!=', 'admin')->paginate(15);

        return view('admin.users', compact('users'));
    }

    // ==========================================
    // 4. ACTIONS (Approve / Reject)
    // ==========================================
    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Changing status to 'confirmed'
        $appointment->status = 'confirmed';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->status = 'rejected';
        // Save the reason from the form (default to 'Clash Date' if empty)
        $appointment->reason = $request->input('reason', 'Clash Date');
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment rejected.');
    }
}
