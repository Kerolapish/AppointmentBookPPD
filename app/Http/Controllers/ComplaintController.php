<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function create()
    {
        // Make sure your folder name matches exactly (Singular vs Plural)
        return view('Complaints.complaint');
    }

    public function store(Request $request)
    {
        // 1. Validate inputs (Added 'purpose')
        $request->validate([
            'ips'           => 'nullable|string|max:100',
            'location'      => 'required|string|max:255',
            'incident_date' => 'required|date|before_or_equal:today',
            'category'      => 'required|string',
            'description'   => 'required|string|max:1000',
            'attachment'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // 2. Handle File Upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaints', 'public');
        }

        // 3. Create Complaint
        Complaint::create([
            'user_id'       => Auth::id(), // ✅ Fixed: added parentheses
            'name'          => Auth::user()->name,
            'email'         => Auth::user()->email,
            'ips'           => $request->ips,
            'purpose'       => $request->purpose,
            'location'      => $request->location,
            'incident_date' => $request->incident_date,
            'category'      => $request->category,
            'description'   => $request->description,
            'attachment'    => $attachmentPath,
        ]);

        return redirect()->back()->with('success', 'Complaint submitted successfully!');
    }

    // --- USER: View My Complaints ---
    public function index()
    {
        $complaints = Complaint::where('user_id', Auth::id()) // ✅ Fixed: added parentheses
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Complaints.my-complaint', compact('complaints'));
    }

    // --- ADMIN: View All Complaints ---
    public function adminIndex()
    {
        $complaints = Complaint::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.complaints', compact('complaints'));
    }

    // --- ADMIN: Mark as Resolved ---
    public function resolve(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $complaint->update([
            'status' => 'resolved',
            'admin_response' => $request->admin_response ?? 'Issue has been resolved.',
        ]);

        return back()->with('success', 'Complaint marked as resolved.');
    }
}
