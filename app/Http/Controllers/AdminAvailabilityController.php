<?php

namespace App\Http\Controllers;

use App\Models\OffDay;
use Illuminate\Http\Request;

class AdminAvailabilityController extends Controller
{
    public function index()
    {
        $offDays = OffDay::orderBy('off_date', 'asc')->get();
        return view('admin.availability', compact('offDays'));
    }

    public function store(Request $request)
    {
        // 1. Validate start and end dates
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255'
        ]);

        // 2. Parse dates using Carbon
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $reason = $request->reason;

        // 3. Loop through every day from start to end
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            // firstOrCreate prevents errors if the date is already blocked
            \App\Models\OffDay::firstOrCreate(
                ['off_date' => $date->format('Y-m-d')],
                ['reason' => $reason]
            );
        }

        return back()->with('success', 'Date range blocked successfully.');
    }

    public function destroy($id)
    {
        OffDay::findOrFail($id)->delete();
        return back()->with('success', 'Date is now available again.');
    }
}
