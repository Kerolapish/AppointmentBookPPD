<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;

        // 1. Get the Counts
        $total = Appointment::where('user_id', $userId)->count();
        
        $pending = Appointment::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $confirmed = Appointment::where('user_id', $userId)
            ->where('status', 'confirmed')
            ->count();

        $monthCount = Appointment::where('user_id', $userId)
            ->whereMonth('date', $currentMonth) 
            ->count();

        // 2. Bundle them into the '$stats' array expected by the View
        $stats = [
            'total' => $total,
            'pending' => $pending,
            'confirmed' => $confirmed,
            'upcoming' => $monthCount // The view labels this box "This Month" but calls it 'upcoming' in code
        ];

        // 3. Get the List (The view calls this variable $upcomingAppointments)
        $upcomingAppointments = Appointment::where('user_id', $userId)
            ->orderBy('date', 'asc') // 'asc' shows nearest future date first
            ->orderBy('time', 'asc')
            ->where('date', '>=', Carbon::today()) // Only show future appointments
            ->take(3)
            ->get();

        // 4. Send both variables to the View
        return view('dashboard', compact('stats', 'upcomingAppointments'));
    }
}