<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in AND their role is specifically 'super_admin'
        // (If your database uses 'admin' instead of 'super_admin', change the word below!)
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return $next($request); // Let them through
        }

        // If they are a normal user, kick them back to the homepage
        return redirect('/')->with('error', 'You do not have permission to access the admin area.');
    }
}
