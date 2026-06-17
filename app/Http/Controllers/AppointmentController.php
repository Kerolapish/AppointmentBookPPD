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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // 1. Show the Booking Form with Availability Data
    public function create()
    {
        $dailyLimit = 5;
        $weeklyLimit = $dailyLimit * 5;

        // Fetch blocked dates (Admin Off Days)
        $blockedDates = OffDay::pluck('off_date')->toArray();

        // Find dates that are fully booked (5 or more appointments)
        $fullyBookedDates = Appointment::select(DB::raw('DATE(date) as appointment_date'))
            ->groupBy('appointment_date')
            ->havingRaw('COUNT(*) >= 5')
            ->pluck('appointment_date')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Find dates the CURRENT USER has already booked
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

        // --- Strict 5-Slot Daily Limit ---
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

        return redirect()->route('my.appointments')->with('success', 'Appointment booked successfully!');
    }

    // 3. View History (Enhanced Search & Filter for User Side)
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $userId = auth()->id();
        $today = \Carbon\Carbon::today()->toDateString();

        // Base upcoming query
        $upcomingQuery = Appointment::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved', 'reschedule_requested'])
            ->where('date', '>=', $today);

        // Base past query
        $pastQuery = Appointment::where('user_id', $userId)
            ->where(function ($q) use ($today) {
                $q->whereIn('status', ['completed', 'rejected', 'cancelled', 'attended'])
                    ->orWhere('date', '<', $today);
            });

        // Applied live search filters across both tabs
        if (!empty($search)) {
            $searchLogic = function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                    ->orWhere('ips', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereRaw("DATE_FORMAT(date, '%W') LIKE ?", ["%{$search}%"]) // Matches day string e.g. Friday
                    ->orWhereRaw("DATE_FORMAT(date, '%d %b') LIKE ?", ["%{$search}%"]) // Matches partial custom dates e.g. 16 Jan
                    ->orWhere('date', 'like', "%{$search}%");
            };

            $upcomingQuery->where($searchLogic);
            $pastQuery->where($searchLogic);
        }

        // Time Filters
        if (!empty($filter)) {
            $filterLogic = function ($q) use ($filter) {
                if ($filter == 'this_week') {
                    $q->whereBetween('date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                } elseif ($filter == 'this_month') {
                    $q->whereMonth('date', \Carbon\Carbon::now()->month)->whereYear('date', \Carbon\Carbon::now()->year);
                }
            };

            $upcomingQuery->where($filterLogic);
            $pastQuery->where($filterLogic);
        }

        $upcoming = $upcomingQuery->orderBy('date', 'asc')->get();
        $past = $pastQuery->orderBy('date', 'desc')->get();

        // Keep standard $appointments pagination query functional for admin roles
        $adminQuery = Appointment::query();
        if (auth()->check() && auth()->user()->role === 'admin') {
            $status = $request->get('status', 'pending');
            $adminQuery->where('status', $status);

            if (!empty($search)) {
                $adminQuery->where(function ($q) use ($search) {
                    $q->where('purpose', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }
            $appointments = $adminQuery->orderBy('created_at', 'desc')->paginate(10);
            return view('Admin.requests', compact('appointments', 'status'));
        }

        $status = $request->get('status', 'pending');
        $appointments = Appointment::where('user_id', $userId)->orderBy('created_at', 'desc')->paginate(10);

        return view('Appointments.my-appointments', compact('appointments', 'status', 'upcoming', 'past'));
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

    // 5. Unified Admin Requests Page Loader Dashboard
    public function appointments(Request $request)
    {
        $pending = Appointment::where('status', 'pending')
            ->orderBy('date', 'asc')
            ->get();

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

        $approved = (clone $historyQuery)->whereIn('status', ['confirmed', 'approved'])->orderBy('date', 'asc')->get();
        $rejected = (clone $historyQuery)->where('status', 'rejected')->orderBy('date', 'desc')->get();

        return view('Admin.requests', compact('pending', 'approved', 'rejected'));
    }

    // 6. Admin Action: Approve Request
    public function approve($id)
    {
        $appointment = Appointment::with('user')->findOrFail($id);
        $appointment->status = 'approved';
        $appointment->handled_by = auth()->id(); // Log the user id of the administrator who approved this item
        $appointment->save();

        try {
            if ($appointment->user && $appointment->user->email) {
                // UPDATE: Added CC functionality for Super Admin and Admin emails
                Mail::to($appointment->user->email)
                    ->cc(['admin@yourdomain.com', 'superadmin@yourdomain.com']) // CHANGE THESE FOR YOUR DEMO
                    ->send(new AppointmentApprovedMail($appointment));
            }
        } catch (\Exception $e) {
            \Log::error("Mail Delivery Failed during approval: " . $e->getMessage());
            return redirect()->back()->with('warning', 'Appointment status updated to Approved, but notification email could not be sent.');
        }

        return redirect()->back()->with('success', 'Appointment approved and email notification sent successfully!');
    }

    // 7. Admin Action: Reject Request
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
        $appointment->handled_by = auth()->id();
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
        $appointment->handled_by = auth()->id();
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

        // Security Gate: Ensure users can only modify their own data
        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 1. Check if the date is blocked by Admin (OffDay/Public Holiday)
        $isBlocked = OffDay::where('off_date', $request->date)->exists();
        if ($isBlocked) {
            return back()->withInput()->withErrors([
                'date' => 'Maaf, tarikh ini telah disekat oleh Admin (Cuti/Tiada di pejabat).'
            ]);
        }

        // 2. Check if total daily appointments have reached the ceiling of 5 slots
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

        // 3. Double-check if this exact hourly time slot is already taken
        $slotTaken = Appointment::where('date', $request->date)
            ->where('time', $request->time)
            ->where('id', '!=', $id)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->exists();

        if ($slotTaken) {
            return back()->withInput()->withErrors([
                'time' => 'This specific time slot is already taken.'
            ]);
        }

        // Apply changes and clear old state flags
        $appointment->date = $request->date;
        $appointment->time = $request->time;
        $appointment->status = 'pending';
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
        try {
            if ($appointment->user && $appointment->user->email) {
                Mail::to($appointment->user->email)->send(new \App\Mail\AppointmentStatusUpdated($appointment, $messageBody));
            }
        } catch (\Exception $e) {
            \Log::error("Production Mail Delivery Failed: " . $e->getMessage());
        }

        try {
            $recipientPhone = $appointment->phone ?? ($appointment->user->phone ?? null);

            if ($recipientPhone) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $recipientPhone);

                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '60' . substr($cleanPhone, 1);
                }

                Http::timeout(5)->post('https://api.yourwhatsappgateway.com/send', [
                    'api_key'   => config('services.whatsapp.key'),
                    'recipient' => $cleanPhone,
                    'message'   => $messageBody
                ]);

                \Log::info("WhatsApp dispatch triggered successfully for: " . $cleanPhone);
            }
        } catch (\Exception $e) {
            \Log::error("Production WhatsApp Gateway Failure: " . $e->getMessage());
        }
    }

    public function activeAppointments(Request $request)
    {
        $query = Appointment::where('status', 'approved');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $appointments = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.partials.appointment-rows', compact('appointments'))->render();
        }

        return view('Admin.active-appointments', compact('appointments'));
    }

    public function complete(Appointment $appointment)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($appointment->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved appointments can be completed.');
        }

        $appointment->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Appointment marked as completed successfully.');
    }
}
