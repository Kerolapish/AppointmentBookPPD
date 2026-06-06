<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\OffDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentApprovedMail;
use Carbon\Carbon;

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
        $userBookedDates = Appointment::where('user_id', Auth::id())
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
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->count();

        if ($dailyCount >= 5) {
            return back()->withInput()->withErrors([
                'date' => 'Sorry, all 5 appointment slots for this date are fully booked. Please select another date.'
            ]);
        }

        // --- Check if the user already booked an appointment on this day ---
        if (Appointment::where('user_id', Auth::id())->where('date', $request->date)->whereNotIn('status', ['cancelled', 'rejected'])->exists()) {
            return back()->withInput()->withErrors(['date' => 'You already have an appointment on this date.']);
        }

        // --- Check if the specific time slot is already taken by someone else ---
        if (Appointment::where('date', $request->date)->where('time', $request->time)->whereNotIn('status', ['cancelled', 'rejected'])->exists()) {
            return back()->withInput()->withErrors(['time' => 'This specific time slot is already taken. Please choose a different time.']);
        }

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

    // 3. View History (Enhanced Search & Filter for User Side)
    public function index(Request $request)
    {
        $query = Appointment::query();
        $status = $request->get('status', 'pending');
        $query->where('status', $status);

        if ($request->has('filter') && $request->filter != '') {
            if ($request->filter == 'this_week') {
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($request->filter == 'this_month') {
                $query->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
            }
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                    ->orWhere('ips', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereDate('date', 'like', "%{$search}%")
                    ->orWhereRaw("DAYNAME(date) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(date, '%d %M %Y') LIKE ?", ["%{$search}%"]);

                if (Schema::hasTable('users')) {
                    $q->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        $appointments = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.appointments.index', compact('appointments', 'status'));
    }

    // 4. Cancel Appointment (User Side)
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

    // 5. Unified Admin Requests Page Loader Dashboard (Fixes variables & mappings)
    public function appointments(Request $request)
    {
        // 1. ALWAYS get all Pending requests (Unfiltered so you never miss new submissions)
        $pending = Appointment::where('status', 'pending')
            ->orderBy('date', 'asc')
            ->get();

        // 2. Start building query for History tables
        $historyQuery = Appointment::query();

        if ($request->filled('search')) {
            $historyQuery->where('name', 'like', $request->search . '%');
        }

        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $historyQuery->whereDate('date', Carbon::today());
                    break;
                case 'week':
                    $historyQuery->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $historyQuery->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
                    break;
            }
        }

        // 3. Unified formatting status values to match what is kept inside your database
        $approved = (clone $historyQuery)->whereIn('status', ['confirmed', 'approved'])->orderBy('date', 'asc')->get();
        $rejected = (clone $historyQuery)->where('status', 'rejected')->orderBy('date', 'desc')->get();

        // Fixed compact variables mapping to match view injection signature perfectly
        return view('Admin.requests', compact('pending', 'approved', 'rejected'));
    }

    // 6. Admin Action: Approve Request
    public function approve($id)
{
    // 1. Find the appointment application
    $appointment = Appointment::with('user')->findOrFail($id);
    
    // 2. Change status to approved/confirmed
    $appointment->status = 'approved'; // Adjust status string to match your database convention
    $appointment->save();

    // 3. Trigger email notification to the applicant
    if ($appointment->user && $appointment->user->email) {
        Mail::to($appointment->user->email)->send(new AppointmentApprovedMail($appointment));
    }

    return redirect()->back()->with('success', 'Appointment approved and email notification sent successfully!');
}

    // 7. Admin Action: Reject Request with Dropdown Reason processing
    public function reject(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $reason = $request->reason;
        if ($reason === 'Other') {
            $request->validate([
                'other_reason' => 'required|string|max:255',
            ]);
            $reason = $request->other_reason;
        }

        $appointment->status = 'rejected';
        $appointment->reject_reason = $reason;
        $appointment->save();

        $msg = "STATUS UPDATE: Your PPD Kluang appointment status has been rejected. Reason: " . $reason;
        $this->sendNotifications($appointment, $msg);

        return redirect()->back()->with('success', 'Appointment rejected successfully.');
    }

    // 8. Admin Action: Send Reschedule Instructions
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $appointment = Appointment::findOrFail($id);

        $appointment->status = 'reschedule_requested';
        $appointment->reschedule_reason = $request->reason;
        $appointment->save();

        $msg = "NOTICE: Admin has requested that you reschedule your appointment. Reason: " . $request->reason . ". Please open your dashboard to choose a new slot.";
        $this->sendNotifications($appointment, $msg);

        return redirect()->back()->with('success', 'Reschedule request sent to user.');
    }

    // 9. User Action: Process Reschedule Update
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

        $isBlocked = OffDay::where('off_date', $request->date)->exists();
        if ($isBlocked) {
            return back()->withInput()->withErrors(['date' => 'Maaf, tarikh ini telah disekat oleh Admin (Cuti/Tiada di pejabat).']);
        }

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

        $slotTaken = Appointment::where('date', $request->date)
            ->where('time', $request->time)
            ->where('id', '!=', $id)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->exists();

        if ($slotTaken) {
            return back()->withInput()->withErrors(['time' => 'This specific time slot is already taken.']);
        }

        $appointment->date = $request->date;
        $appointment->time = $request->time;
        $appointment->status = 'pending'; // Reset back to admin queue
        $appointment->reschedule_reason = null;
        $appointment->save();

        return redirect()->route('dashboard')->with('success', 'New appointment time submitted! Waiting for admin approval.');
    }

    // 10. Fetch Live Booked Times (API for JS Component)
    public function getBookedTimes(Request $request)
    {
        $date = $request->query('date');

        $bookedTimes = Appointment::where('date', $date)
            ->whereIn('status', ['pending', 'approved', 'confirmed'])
            ->pluck('time')
            ->map(function ($time) {
                return \Carbon\Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        return response()->json($bookedTimes);
    }

    // 11. Notification Dispatched Manager
    private function sendNotifications($appointment, $messageBody)
    {
        // Email Dispatch Channel
        try {
            if ($appointment->user && $appointment->user->email) {
                Mail::to($appointment->user->email)->send(new \App\Mail\AppointmentStatusUpdated($appointment, $messageBody));
            }
        } catch (\Exception $e) {
            \Log::error("Production Mail Delivery Failed: " . $e->getMessage());
        }

        // WhatsApp Gateway Channel
        try {
            if ($appointment->user && isset($appointment->user->phone_number)) {
                Http::timeout(5)->post('https://api.yourwhatsappgateway.com/send', [
                    'api_key' => config('services.whatsapp.key'),
                    'recipient' => $appointment->user->phone_number,
                    'message' => $messageBody
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Production WhatsApp Gateway Failure: " . $e->getMessage());
        }
    }
}
