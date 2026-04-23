<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // This will load a view specifically for the super admin
        return view('Admin.dashboard'); 
    }
}
