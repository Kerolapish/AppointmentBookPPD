<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the form inputs
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'max:15'], // Validating the FORM input name
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone_number, // <--- CRITICAL FIX: Save form input 'phone_number' to DB column 'phone'
            'password' => Hash::make($request->password),
            'role' => 'user', // <--- FIXED: Hardcoded default role since form doesn't provide it
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Account created successfully! Please login.');
    }
}