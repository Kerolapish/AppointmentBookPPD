<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // Stats Calculation
        $stats = [
            'pending'   => Appointment::where('user_id', $userId)->where('status', 'pending')->count(),
            'confirmed' => Appointment::where('user_id', $userId)->whereIn('status', ['approved', 'confirmed'])->count(),
            'upcoming'  => Appointment::where('user_id', $userId)->where('date', '>=', $now->toDateString())->count(),
        ];

        $totalAppointments = Appointment::where('user_id', $userId)->count();

        // Fetch Appointments for the list
        $upcomingAppointments = Appointment::where('user_id', $userId)
            ->where(function ($query) use ($now) {
                $query->where('date', '>=', $now->toDateString())
                    ->orWhereIn('status', ['rejected', 'reschedule_requested']);
            })
            ->orderBy('date', 'desc')
            ->get();

        // Calendar Data (Fixed Table Missing Error)
        $fullyBookedDates = Appointment::select('date')
            ->where('date', '>=', $now->toDateString())
            ->groupBy('date')
            ->having(DB::raw('count(*)'), '>=', 5)
            ->pluck('date')->toArray();

        $userBookedDates = Appointment::where('user_id', $userId)
            ->where('date', '>=', $now->toDateString())
            ->pluck('date')->toArray();

        return view('dashboard', [
            'stats' => $stats,
            'totalAppointments' => $totalAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'blockedDates' => [],
            'fullyBookedDates' => $fullyBookedDates,
            'userBookedDates' => $userBookedDates,
            'percentageChange' => 100 // Example static value
        ]);
    }
}
