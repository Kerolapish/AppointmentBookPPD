<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // 1. Show the Booking Form with Availability Data
    public function create()
    {
        $dailyLimit = 15;
        $weeklyLimit = $dailyLimit * 5;

        // Calculate Today
        $todayCount = Appointment::whereDate('date', Carbon::today())->count();
        $todayLeft = max(0, $dailyLimit - $todayCount);

        // Calculate Tomorrow
        $tomorrowCount = Appointment::whereDate('date', Carbon::tomorrow())->count();
        $tomorrowLeft = max(0, $dailyLimit - $tomorrowCount);

        // Calculate This Week
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();
        $weekCount = Appointment::whereBetween('date', [$startOfWeek, $endOfWeek])->count();

        if ($weekCount >= ($weeklyLimit * 0.9)) {
            $weekStatus = 'Fully Booked';
            $weekColor = 'text-red-600';
        } elseif ($weekCount >= ($weeklyLimit * 0.5)) {
            $weekStatus = 'Limited Slots';
            $weekColor = 'text-orange-500';
        } else {
            $weekStatus = 'Open Availability';
            $weekColor = 'text-green-600';
        }

        return view('Appointments.appointments', compact(
            'todayLeft',
            'tomorrowLeft',
            'weekStatus',
            'weekColor'
        ));
    }

    // 2. Save the Appointment
    public function store(Request $request)
    {
        $request->validate([
            'purpose'       => 'required|string|max:255',
            'other_purpose' => 'nullable|string|max:255',
            'ips'           => 'required|string|max:255',
            'location'      => 'required|string',
            'phone'         => 'required|string|max:15',
            // 1. Basic Date Validation
            'date'          => [
                'required',
                'date',
                'after_or_equal:today',
                // 2. Custom Rule: Ensure it's a weekday (Mon-Fri)
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isWeekend()) {
                        $fail('Appointments can only be booked on weekdays (Monday to Friday).');
                    }
                },
            ],
            // 3. Time Validation based on the selected date
            'time'          => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if (!$request->date || Carbon::parse($request->date)->isWeekend()) {
                        return;
                    }

                    $date = Carbon::parse($request->date);
                    $time = Carbon::parse($value);
                    $dayOfWeek = $date->dayOfWeek; // 1 (Mon) to 5 (Fri)

                    if ($dayOfWeek === Carbon::FRIDAY) {
                        // Friday Hours: 8:00-12:15 AND 14:45-17:00
                        $morningStart = Carbon::createFromTime(8, 0);
                        $morningEnd   = Carbon::createFromTime(12, 15);
                        $afternoonStart = Carbon::createFromTime(14, 45);
                        $afternoonEnd   = Carbon::createFromTime(17, 0);
                    } else {
                        // Mon-Thu Hours: 8:00-13:00 AND 14:00-17:00
                        $morningStart = Carbon::createFromTime(8, 0);
                        $morningEnd   = Carbon::createFromTime(13, 0);
                        $afternoonStart = Carbon::createFromTime(14, 0);
                        $afternoonEnd   = Carbon::createFromTime(17, 0);
                    }

                    $isMorning = $time->betweenIncluded($morningStart, $morningEnd);
                    $isAfternoon = $time->betweenIncluded($afternoonStart, $afternoonEnd);

                    if (!($isMorning || $isAfternoon)) {
                        $fail("The selected time is outside of office hours or during the lunch break.");
                    }
                },
            ],
        ]);

        // --- LOGIC: Determine final purpose ---
        $finalPurpose = $request->purpose;
        if ($request->purpose === 'Other' && $request->filled('other_purpose')) {
            $finalPurpose = $request->other_purpose;
        }

        // --- Check Redundancy ---
        $userAlreadyBooked = Appointment::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($userAlreadyBooked) {
            return back()->withInput()->withErrors(['date' => 'You already have an appointment scheduled for this date.']);
        }

        $slotTaken = Appointment::where('date', $request->date)
            ->where('time', $request->time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($slotTaken) {
            return back()->withInput()->withErrors(['time' => 'Sorry, this time slot is already taken.']);
        }

        // --- Save (Fixed: Removed the Duplicate Create Call) ---
        Appointment::create([
            'user_id'  => Auth::id(),
            'name'     => Auth::user()->name,
            'phone'    => $request->phone,
            'purpose'  => $finalPurpose,
            'ips'      => $request->ips,
            'location' => $request->location,
            'date'     => $request->date,
            'time'     => $request->time,
            'status'   => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Appointment booked successfully!');
    }

    // 3. View History (Enhanced Search & Filter)
    public function index(Request $request)
    {
        $query = Appointment::where('user_id', Auth::id());

        // --- 1. Filter Logic (Week / Month) ---
        if ($request->has('filter') && $request->filter != '') {
            if ($request->filter == 'this_week') {
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($request->filter == 'this_month') {
                $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
            }
        }

        // --- 2. Search Logic (Includes Date & Day Name) ---
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                    ->orWhere('ips', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    // Search by strict date (e.g., 2026-01-16)
                    ->orWhereDate('date', 'like', "%{$search}%")
                    // Search by Day Name (e.g., "Monday", "Friday")
                    ->orWhereRaw("DAYNAME(date) LIKE ?", ["%{$search}%"])
                    // Search by Readable Date (e.g., "16 Jan")
                    ->orWhereRaw("DATE_FORMAT(date, '%d %M %Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Get results
        $allAppointments = $query->orderBy('date', 'asc')->get();

        // Split into Upcoming and Past
        $upcoming = $allAppointments->filter(function ($appt) {
            return Carbon::parse($appt->date)->gte(Carbon::today());
        });

        $past = $allAppointments->filter(function ($appt) {
            return Carbon::parse($appt->date)->lt(Carbon::today());
        });

        return view('Appointments.my-appointments', compact('upcoming', 'past'));
    }

    // 4. Cancel Appointment
    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($appointment->status != 'pending') {
            return back()->with('error', 'You can only cancel pending appointments.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    // 5. Show Reschedule Form
    public function reschedule($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($appointment->status == 'completed') {
            return redirect()->back()->with('error', 'Completed appointments cannot be rescheduled.');
        }

        // Ensure this view path matches your actual folder structure
        return view('Appointments.reschedule', compact('appointment'));
    }

    // 6. Process Reschedule Update (The "Smart" Logic)
    public function updateReschedule(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id != Auth::id()) {
            abort(403);
        }

        // Validation: Fields are 'nullable' so user isn't forced to change both
        $request->validate([
            'new_date' => 'nullable|date|after_or_equal:today',
            'new_time' => 'nullable',
        ]);

        // Logic: Use new input if provided; otherwise, keep old value
        $finalDate = $request->filled('new_date') ? $request->new_date : $appointment->date;
        $finalTime = $request->filled('new_time') ? $request->new_time : $appointment->time;

        // Check if the NEW combination is already taken (Optional safety check)
        $slotTaken = Appointment::where('date', $finalDate)
            ->where('time', $finalTime)
            ->where('id', '!=', $id) // Ignore self
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($slotTaken) {
            return back()->with('error', 'The chosen date and time slot is already booked.');
        }

        // Update
        $appointment->update([
            'date'   => $finalDate,
            'time'   => $finalTime,
            'status' => 'pending', // Reset to pending for admin approval
        ]);

        return redirect()->route('dashboard')->with('success', 'Appointment rescheduled successfully!');
    }

    public function reject(Request $request, $id)
    {
        // 1. Find the appointment
        $appointment = Appointment::findOrFail($id);

        // 2. Determine the reason
        // If user selected "Other", use the text area input ('other_reason')
        // Otherwise, use the dropdown value ('reason')
        $reason = $request->reason;
        if ($reason === 'Other') {
            $request->validate([
                'other_reason' => 'required|string|max:255',
            ]);
            $reason = $request->other_reason;
        }

        // 3. Update the appointment status
        $appointment->status = 'rejected';
        $appointment->reject_reason = $reason;
        $appointment->save();

        // 4. Redirect back with a message
        return back()->with('success', 'Appointment rejected successfully.');
    }

    public function appointments(Request $request)
    {
        // 1. ALWAYS get all Pending requests (Unfiltered)
        // This ensures you never miss an incoming request while searching
        $pending = Appointment::where('status', 'pending')
            ->orderBy('date', 'asc')
            ->get();

        // 2. Start a query for History (Approved & Rejected)
        $historyQuery = Appointment::query();

        // --- Apply Search (Only to History) ---
        if ($request->filled('search')) {
            $historyQuery->where('name', 'like', $request->search . '%');
        }

        // --- Apply Date Filter (Only to History) ---
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $historyQuery->whereDate('date', Carbon::today());
                    break;
                case 'week':
                    $historyQuery->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $historyQuery->whereMonth('date', Carbon::now()->month)
                        ->whereYear('date', Carbon::now()->year);
                    break;
            }
        }

        // 3. Get the results derived from the Filtered History Query
        $approved = (clone $historyQuery)->where('status', 'approved')->orderBy('date', 'asc')->get();
        $rejected = (clone $historyQuery)->where('status', 'rejected')->orderBy('date', 'desc')->get();

        return view('admin.requests', compact('pending', 'approved', 'rejected'));
    }
}
