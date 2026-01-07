<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return view('auth.login');
});

// 1. Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 2. Book Appointment Routes
// SHOW the form
Route::get('/book-appointment', [AppointmentController::class, 'create'])
    ->middleware(['auth'])
    ->name('appointments.create'); // I updated this name to match standard conventions

// SAVE the form
Route::post('/book-appointment', [AppointmentController::class, 'store'])
    ->middleware(['auth'])
    ->name('appointments.store');

// 3. My Bookings Route (UPDATED)
// Connected to the 'index' method in AppointmentController to show the history list
Route::get('/my-appointments', [AppointmentController::class, 'index'])
    ->middleware(['auth'])
    ->name('my.appointments'); 

// 4. Cancel Appointment Route (NEW)
// This allows the 'Cancel' button to work using the DELETE or PATCH method
Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])
    ->middleware(['auth'])
    ->name('appointments.cancel');

require __DIR__.'/auth.php';

// Debug Route (You can keep this for now)
Route::get('/check-files', function() {
    $files = scandir(resource_path('views'));
    dd($files);
});