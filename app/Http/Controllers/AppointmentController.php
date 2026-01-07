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

        // 1. Calculate Today
        $todayCount = Appointment::whereDate('date', Carbon::today())->count();
        $todayLeft = max(0, $dailyLimit - $todayCount);

        // 2. Calculate Tomorrow
        $tomorrowCount = Appointment::whereDate('date', Carbon::tomorrow())->count();
        $tomorrowLeft = max(0, $dailyLimit - $tomorrowCount);

        // 3. Calculate This Week
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
            'todayLeft', 'tomorrowLeft', 'weekStatus', 'weekColor'
        ));
    }

    // 2. Save the Appointment
    public function store(Request $request)
    {
        $request->validate([
            'purpose'  => 'required|string|max:255',
            'ips'      => 'required|string|max:255',
            'location' => 'required|string',
            'date'     => 'required|date',
            'time'     => 'required',
            'phone'    => 'required|string|max:15',
        ]);

        // Check Individual Redundancy
        $userAlreadyBooked = Appointment::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($userAlreadyBooked) {
            return back()->withInput()->withErrors(['date' => 'You already have an appointment scheduled for this date.']);
        }

        // Check Strict Time Slot
        $slotTaken = Appointment::where('date', $request->date)
            ->where('time', $request->time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($slotTaken) {
            return back()->withInput()->withErrors(['time' => 'Sorry, the ' . $request->time . ' slot is already taken.']);
        }

        Appointment::create([
            'user_id'  => Auth::id(),
            'name'     => Auth::user()->name,
            'phone'    => $request->phone,
            'purpose'  => $request->purpose,
            'ips'      => $request->ips,
            'location' => $request->location,
            'date'     => $request->date,
            'time'     => $request->time,
            'status'   => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Appointment booked successfully!');
    }

    // 3. View History (FIXED THIS FUNCTION)
    public function index(Request $request)
    {
        $query = Appointment::where('user_id', Auth::id());

        // Search Logic
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                    ->orWhere('ips', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $allAppointments = $query->orderBy('date', 'asc')->get();

        // --- FIX: Use 'gte' (Greater Than or Equal) instead of 'isSameOrAfter' ---
        $upcoming = $allAppointments->filter(function ($appt) {
            return Carbon::parse($appt->date)->gte(Carbon::today());
        });

        // --- FIX: Use 'lt' (Less Than) instead of 'isBefore' ---
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
}