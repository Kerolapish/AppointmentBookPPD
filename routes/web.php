<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAvailabilityController;

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
    Route::get('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::put('/appointments/{id}/reschedule', [AppointmentController::class, 'updateReschedule'])->name('appointments.updateReschedule');
    Route::get('/my-appointments', [AppointmentController::class, 'index'])->name('my.appointments');
    Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointment/{id}/update-time', [AppointmentController::class, 'updateTime'])->name('user.appointment.updateTime');
});

// PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update.profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ADMIN ROUTES
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/requests', [AdminController::class, 'appointments'])->name('admin.requests');
    Route::patch('/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/appointment/{id}/request-reschedule', [AdminController::class, 'requestReschedule'])->name('admin.appointment.reschedule');
    Route::patch('/appointment/{id}/reject', [AdminController::class, 'reject'])->name('admin.appointment.reject');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/report/pdf', [AdminController::class, 'downloadReportPdf'])->name('admin.report.pdf');
    Route::get('/availability', [AdminAvailabilityController::class, 'index'])->name('admin.availability');
    Route::post('/availability', [AdminAvailabilityController::class, 'store'])->name('admin.availability.store');
    Route::delete('/availability/{id}', [AdminAvailabilityController::class, 'destroy'])->name('admin.availability.delete');
});

require __DIR__ . '/auth.php';
