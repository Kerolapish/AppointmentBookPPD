<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    // Show the form
    public function create()
    {
        return view('complaint');
    }

    // Insert data to database
    public function store(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'ips' => 'nullable|string',
            'purpose' => 'required|string',
            'location' => 'required|string',
        ]);

        // 2. Insert into Database
        Complaint::create([
            'name' => $request->name,
            'email' => $request->email,
            'ips' => $request->ips,
            'purpose' => $request->purpose,
            'location' => $request->location,
        ]);

        // 3. Redirect with success message
        return redirect()->back()->with('success', 'Complaint submitted successfully!');
    }
}