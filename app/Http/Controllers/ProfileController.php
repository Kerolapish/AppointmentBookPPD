<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // This opens the editable form (profile/edit.blade.php)
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile (Read-only).
     */
    public function show(Request $request): View
    {
        // This opens the read-only page (profile/show.blade.php)
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        // 1. Validate inputs (Change 'ips_name' to 'ips' here if DB column is 'ips')
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'ips_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        // 2. Save changes
        $request->user()->fill($validated);
        $request->user()->save();

        // 3. Redirect
        return redirect()->route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
