<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\OffDay;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    // 1. Show the Booking Form with Availability Data
    public function create()
    {
        $dailyLimit = 5;
        $weeklyLimit = $dailyLimit * 5;

        // 1. Fetch blocked dates (Admin Off Days)
        $blockedDates = OffDay::pluck('off_date')->toArray();

        // 2. Find dates that are fully booked (5 or more appointments)
        $fullyBookedDates = Appointment::select(DB::raw('DATE(date) as appointment_date'))
            ->groupBy('appointment_date')
            ->havingRaw('COUNT(*) >= 5')
            ->pluck('appointment_date')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // 3. Find dates the CURRENT USER has already booked
        $userBookedDates = Appointment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->select(DB::raw('DATE(date) as appointment_date'))
            ->pluck('appointment_date')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // --- Weekly/Daily Availability Logic ---
        $todayCount = Appointment::whereDate('date', Carbon::today())->count();
        $todayLeft = max(0, $dailyLimit - $todayCount);

        $tomorrowCount = Appointment::whereDate('date', Carbon::tomorrow())->count();
        $tomorrowLeft = max(0, $dailyLimit - $tomorrowCount);

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

        // 4. Return the view with ALL arrays
        return view('Appointments.appointments', compact(
            'todayLeft',
            'tomorrowLeft',
            'weekStatus',
            'weekColor',
            'blockedDates',
            'fullyBookedDates',
            'userBookedDates'
        ));
    }

    // 2. Save the Appointment
    public function store(Request $request)
    {
        $request->validate([
            'purpose'       => 'required|string|max:255',
            'ips'           => 'required|string|max:255',
            'location'      => 'required|string',
            'phone'         => 'required|string|max:15',
            'date'          => 'required|date|after_or_equal:today',
            'time'          => 'required',
        ]);

        // --- Security Check for Blocked Dates ---
        $isBlocked = OffDay::where('off_date', $request->date)->exists();
        if ($isBlocked) {
            return back()->withInput()->withErrors(['date' => 'Maaf, tarikh ini telah disekat oleh Admin (Cuti/Tiada di pejabat).']);
        }

        // --- NEW: Strict 5-Slot Daily Limit ---
        $dailyCount = Appointment::where('date', $request->date)
            ->whereNotIn('status', ['cancelled', 'rejected']) // Don't count cancelled/rejected ones
            ->count();

        if ($dailyCount >= 5) {
            return back()->withInput()->withErrors([
                'date' => 'Sorry, all 5 appointment slots for this date are fully booked. Please select another date.'
            ]);
        }

        // --- RESTORED: Check if the user already booked an appointment on this day ---
        if (Appointment::where('user_id', Auth::id())->where('date', $request->date)->whereNotIn('status', ['cancelled', 'rejected'])->exists()) {
            return back()->withInput()->withErrors(['date' => 'You already have an appointment on this date.']);
        }

        // --- RESTORED: Check if the specific time slot is already taken by someone else ---
        if (Appointment::where('date', $request->date)->where('time', $request->time)->whereNotIn('status', ['cancelled', 'rejected'])->exists()) {
            return back()->withInput()->withErrors(['time' => 'This specific time slot is already taken. Please choose a different time.']);
        }

        // --- Rest of your existing logic ---
        $finalPurpose = $request->purpose === 'Other' ? $request->other_purpose : $request->purpose;

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

        $request->validate([
            'new_date' => 'nullable|date|after_or_equal:today',
            'new_time' => 'nullable',
        ]);

        $finalDate = $request->filled('new_date') ? $request->new_date : $appointment->date;
        $finalTime = $request->filled('new_time') ? $request->new_time : $appointment->time;

        // --- 1. Security Check for Blocked Dates ---
        $isBlocked = OffDay::where('off_date', $finalDate)->exists();
        if ($isBlocked) {
            return back()->withInput()->withErrors(['new_date' => 'Maaf, tarikh ini telah disekat oleh Admin (Cuti/Tiada di pejabat).']);
        }

        // --- 2. Check Daily 5-Slot Limit (Only if they are changing the date) ---
        if ($finalDate != $appointment->date) {
            $dailyCount = Appointment::where('date', $finalDate)
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->count();

            if ($dailyCount >= 5) {
                return back()->withInput()->withErrors([
                    'new_date' => 'Sorry, all 5 appointment slots for your new date are fully booked.'
                ]);
            }
        }

        // --- 3. Check if the specific time slot is already taken ---
        $slotTaken = Appointment::where('date', $finalDate)
            ->where('time', $finalTime)
            ->where('id', '!=', $id) // Ignore their own current appointment
            ->whereNotIn('status', ['cancelled', 'rejected'])
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
        $approved = (clone $historyQuery)->where('status', 'confirmed')->orderBy('date', 'asc')->get();
        $rejected = (clone $historyQuery)->where('status', 'rejected')->orderBy('date', 'desc')->get();

        return view('admin.requests', compact('pending', 'confirmed', 'rejected'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today', // Fixed column name
            'time' => 'required',                           // Fixed column name
        ]);

        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);

        $appointment->update([
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending', // Send it back to the admin
            'reschedule_reason' => null // Clear the reason since it's resolved
        ]);

        return redirect()->route('dashboard')->with('success', 'Appointment rescheduled successfully and is pending admin approval.');
    }

    public function updateTime(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // --- 1. Security Check for Blocked Dates ---
        $isBlocked = OffDay::where('off_date', $request->date)->exists();
        if ($isBlocked) {
            return back()->withInput()->withErrors(['date' => 'Maaf, tarikh ini telah disekat oleh Admin (Cuti/Tiada di pejabat).']);
        }

        // --- 2. Check Daily 5-Slot Limit (Only if changing the date) ---
        if ($request->date != $appointment->date) {
            $dailyCount = Appointment::where('date', $request->date)
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->count();

            if ($dailyCount >= 5) {
                return back()->withInput()->withErrors([
                    'date' => 'Sorry, all 5 appointment slots for this new date are fully booked.'
                ]);
            }
        }

        // --- 3. Check if specific time slot is taken ---
        $slotTaken = Appointment::where('date', $request->date)
            ->where('time', $request->time)
            ->where('id', '!=', $id) // Ignore self
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->exists();

        if ($slotTaken) {
            return back()->withInput()->withErrors(['time' => 'This specific time slot is already taken.']);
        }

        // Update the details
        $appointment->date = $request->date;
        $appointment->time = $request->time;
        $appointment->status = 'pending';
        $appointment->reschedule_reason = null;
        $appointment->save();

        return redirect()->route('dashboard')->with('success', 'New appointment time submitted! Waiting for admin approval.');
    }

    public function getBookedTimes(Request $request)
    {
        $date = $request->query('date');

        // Fetch times that are already taken for this date
        // We only care about pending or approved appointments
        $bookedTimes = \App\Models\Appointment::where('date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('time') // This gets the 'time' column values
            ->map(function ($time) {
                // This ensures the time format matches your JavaScript array exactly (e.g. "08:00")
                return \Carbon\Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        return response()->json($bookedTimes);
    }
}
