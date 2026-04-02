<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Added this

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Check if the user's role is in the allowed list
        $userRole = Auth::user()->role;
        
        if (!in_array($userRole, $roles)) {
            // Kick them to their own dashboard if they try to enter the wrong area
            return match($userRole) {
                'superadmin' => redirect()->route('superadmin.dashboard'),
                'admin'      => redirect()->route('admin.dashboard'),
                default      => redirect()->route('dashboard'),
            };
        }

        return $next($request);
    }
}