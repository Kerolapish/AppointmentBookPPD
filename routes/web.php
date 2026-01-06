<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// This displays the login page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Route to show the registration form
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth',])->name('dashboard');

/* routes/web.php */

// The Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// The Appointment Booking Page
Route::get('/appointments', function () {
    return view('appointments'); // Looks for resources/views/appointments.blade.php
})->name('appointments');

// The Appointment Booking Page
Route::get('/complaint', function () {
    return view('complaint'); // Looks for resources/views/appointments.blade.php
})->name('complaint');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
