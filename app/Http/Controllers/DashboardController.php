<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $userId = Auth::id();

    // 1. Calculate Percentage
    $currentMonthCount = Appointment::where('user_id', $userId)
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->count();

    $lastMonthCount = Appointment::where('user_id', $userId)
        ->whereMonth('created_at', Carbon::now()->subMonth()->month)
        ->whereYear('created_at', Carbon::now()->subMonth()->year)
        ->count();

    if ($lastMonthCount > 0) {
        $percentageChange = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
    } else {
        $percentageChange = $currentMonthCount > 0 ? 100 : 0;
    }

    // 2. Fetch Stats
    $stats = [
        'pending'   => Appointment::where('user_id', $userId)->where('status', 'pending')->count(),
        'confirmed' => Appointment::where('user_id', $userId)->where('status', 'confirmed')->count(),
        'upcoming'  => Appointment::where('user_id', $userId)->where('date', '>=', Carbon::now())->count(),
    ];

    // 3. Fetch Upcoming Appointments (The missing part!)
    $upcomingAppointments = Appointment::where('user_id', $userId)
        ->where('date', '>=', Carbon::now())
        ->orderBy('date', 'asc') // Show nearest dates first
        ->orderBy('time', 'asc')
        ->take(3) // Limit to top 3
        ->get();

    // 4. Return View
    return view('dashboard', [
        'stats'                => $stats,
        'totalAppointments'    => Appointment::where('user_id', $userId)->count(),
        'percentageChange'     => round($percentageChange, 1),
        'upcomingAppointments' => $upcomingAppointments, // <--- Now accessible in view
    ]);
}
}