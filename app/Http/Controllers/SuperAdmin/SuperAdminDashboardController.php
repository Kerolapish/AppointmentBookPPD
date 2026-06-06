<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\BlockedDate;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Calculate the Analytics
        $totalUsers = \App\Models\User::count();
        $newUsersToday = \App\Models\User::whereDate('created_at', today())->count();

        $totalAppointments = \App\Models\Appointment::count();
        $pendingAppointments = \App\Models\Appointment::where('status', 'pending')->count();

        // 2. Fetch the appointments 
        $appointments = \App\Models\Appointment::orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->paginate(15);

        // 3. Pass EVERYTHING to the view
        return view('SuperAdmin.dashboard', compact(
            'totalUsers',
            'newUsersToday',
            'totalAppointments',
            'pendingAppointments',
            'appointments'
        ));
    }

    // NEW METHOD: Cancel/Delete the appointment
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        // Redirect back to the dashboard with a success message
        return redirect()->back()->with('success', 'Appointment has been cancelled successfully.');
    }

    // NEW METHOD: User Management
    public function users(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('SuperAdmin.users', compact('users', 'search'));
    }

    // NEW METHOD: Update User Data
    public function updateUser(Request $request, $id)
    {
        // 1. Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:user,admin,super_admin',
        ]);

        // 2. Find the user and update their details
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        // 3. Send them back to the table with a success message
        return redirect()->route('super_admin.users')->with('success', 'User updated successfully!');
    }

    // ==========================================
    // AVAILABILITY & BLOCKED DATES
    // ==========================================

    // 1. Show the page
    // ==========================================
    // AVAILABILITY & BLOCKED DATES (FIXED)
    // ==========================================

    // 1. Show the page
    public function availability()
    {
        // Use OffDay instead of BlockedDate, and 'off_date' instead of 'date'
        $blockedDates = \App\Models\OffDay::where('off_date', '>=', now()->toDateString())
            ->orderBy('off_date', 'asc')
            ->paginate(15);

        return view('SuperAdmin.availability', compact('blockedDates'));
    }

    // 2. Save a new blocked date
    // 2. Save a new blocked date (Single or Date Range)
    public function storeBlockedDate(Request $request)
    {
        // Validate inputs based on mode selection
        $request->validate([
            'mode'       => 'required|in:single,range',
            'date'       => 'required_if:mode,single|date|nullable',
            'start_date' => 'required_if:mode,range|date|nullable',
            'end_date'   => 'required_if:mode,range|date|after_or_equal:start_date|nullable',
            'reason'     => 'nullable|string|max:255',
        ]);

        $reason = $request->input('reason', 'Blocked Day');

        // Handle Single Date Selection
        if ($request->mode === 'single') {
            $request->validate([
                'date' => 'unique:off_days,off_date'
            ], [
                'date.unique' => 'This date is already blocked.'
            ]);

            \App\Models\OffDay::firstOrCreate(
                ['off_date' => \Carbon\Carbon::parse($request->date)->format('Y-m-d')],
                ['reason' => $reason]
            );
        } 
        // Handle Multiple Date Range Selection
        elseif ($request->mode === 'range') {
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                \App\Models\OffDay::firstOrCreate(
                    ['off_date' => $date->format('Y-m-d')],
                    ['reason' => $reason]
                );
            }
        }

        return redirect()->route('super_admin.availability')->with('success', 'Blocked dates updated successfully!');
    }

    // 3. Delete a blocked date
    public function destroyBlockedDate($id)
    {
        \App\Models\OffDay::findOrFail($id)->delete();
        return redirect()->route('super_admin.availability')->with('success', 'Blocked date removed!');
    }

    public function dashboard()
    {
        // 1. Calculate the Analytics
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', today())->count();

        // Assuming your appointments table has a 'status' column
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();

        // 2. Fetch any other data you already had (like your paginated appointments)
        // $appointments = Appointment::latest()->paginate(10); 

        // 3. Pass everything to the view
        return view('SuperAdmin.dashboard', compact(
            'totalUsers',
            'newUsersToday',
            'totalAppointments',
            'pendingAppointments'
            // , 'appointments' <-- keep whatever else you were passing before
        ));
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'status' => 'required|in:pending,approved,cancelled'
        ]);

        $appointment = \App\Models\Appointment::findOrFail($id);
        $appointment->update([
            'date' => $request->date,
            'time' => $request->time,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Appointment updated successfully!');
    }

    public function reports()
    {
        // Basic Stats
        $totalUsers = User::count();
        $totalAppointments = Appointment::count();
        $cancelledCount = Appointment::where('status', 'cancelled')->count();
        $approvedCount = Appointment::where('status', 'approved')->count();

        // Data for Chart (Last 6 Months)
        $months = [];
        $appointmentCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $appointmentCounts[] = Appointment::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->count();
        }

        return view('SuperAdmin.reports', compact(
            'totalUsers',
            'totalAppointments',
            'cancelledCount',
            'approvedCount',
            'months',
            'appointmentCounts'
        ));
    }

    public function exportAppointments()
    {
        $appointments = Appointment::with('user')->get();
        $fileName = 'appointments_report_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'User Name', 'Purpose', 'Date', 'Time', 'Status'];

        $callback = function () use ($appointments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($appointments as $app) {
                fputcsv($file, [$app->id, $app->user->name ?? 'N/A', $app->purpose, $app->date, $app->time, $app->status]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
