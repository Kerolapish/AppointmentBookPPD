<?php

namespace App\Http\Controllers;

use App\Models\OffDay;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAvailabilityController extends Controller
{
    public function index()
    {
        // Change variable name from $blockedDates to $offDays
        $offDays = OffDay::orderBy('off_date', 'asc')->paginate(10);
        return view('admin.availability', compact('offDays'));
    }

    public function store(Request $request)
    {
        // 1. Validate inputs based on mode selection
        $request->validate([
            'mode'       => 'required|in:single,range',
            'off_date'   => 'required_if:mode,single|date|nullable',
            'start_date' => 'required_if:mode,range|date|nullable',
            'end_date'   => 'required_if:mode,range|date|after_or_equal:start_date|nullable',
            'reason'     => 'nullable|string|max:255'
        ]);

        $reason = $request->input('reason', 'Admin Blocked Day');

        // 2. Handle Single Date Selection
        if ($request->mode === 'single') {
            OffDay::firstOrCreate(
                ['off_date' => Carbon::parse($request->off_date)->format('Y-m-d')],
                ['reason' => $reason]
            );
        }
        // 3. Handle Multiple Date Range Selection (Super Admin feature)
        elseif ($request->mode === 'range') {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                OffDay::firstOrCreate(
                    ['off_date' => $date->format('Y-m-d')],
                    ['reason' => $reason]
                );
            }
        }

        return back()->with('success', 'Blocked dates updated successfully.');
    }

    public function destroy($id)
    {
        OffDay::findOrFail($id)->delete();
        return back()->with('success', 'Date is now available again.');
    }
}
