<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController; // <--- Don't forget this!

Route::get('/', function () {
    return view('auth.login');
});

// USER DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// APPOINTMENT ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/book-appointment', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/book-appointment', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/my-appointments', [AppointmentController::class, 'index'])->name('my.appointments');
    Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

// PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ADMIN ROUTES GROUP
// The 'prefix' => 'admin' means all URLs here start with /admin
// routes/web.php

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Requests Page
    Route::get('/requests', [AdminController::class, 'appointments'])->name('admin.appointments');

    // Actions (NOTE: We removed the extra '/admin' here)
    Route::patch('/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::patch('/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    // User List
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
});

require __DIR__ . '/auth.php';
