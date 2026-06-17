<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\AdminAvailabilityController;

Route::get('/', function () {
    return view('auth.login');
});

// USER DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// APPOINTMENT ROUTES (USER SIDE)
Route::middleware(['auth'])->group(function () {
    Route::get('/book-appointment', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/book-appointment', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::put('/appointments/{id}/reschedule', [AppointmentController::class, 'updateReschedule'])->name('appointments.updateReschedule');
    Route::get('/my-appointments', [AppointmentController::class, 'index'])->name('my.appointments');
    Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointment/{id}/update-time', [AppointmentController::class, 'updateTime'])->name('user.appointment.updateTime');
    Route::get('/get-booked-times', [AppointmentController::class, 'getBookedTimes'])->name('appointments.booked-times');
    Route::patch('/appointment/{appointment}/update-time', [AppointmentController::class, 'updateTime'])->name('appointments.update-time');
    Route::get('/appointments/booked-times', [AppointmentController::class, 'getBookedTimes'])->name('appointments.booked-times');
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
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/report/pdf', [AdminController::class, 'downloadReportPdf'])->name('admin.report.pdf');

    // --- Appointment Actions Routed to AppointmentController ---
    Route::get('/requests', [AppointmentController::class, 'appointments'])->name('admin.requests');
    Route::patch('/approve/{id}', [AppointmentController::class, 'approve'])->name('admin.approve');
    Route::patch('/appointment/{id}/reject', [AppointmentController::class, 'reject'])->name('admin.appointment.reject');
    
    // FIX: Directed to AdminController@requestReschedule instead of AppointmentController@reschedule
    Route::patch('/appointment/{id}/request-reschedule', [AdminController::class, 'requestReschedule'])->name('admin.appointment.reschedule');

    // --- Admin Availability Block ---
    Route::get('/availability', [AdminAvailabilityController::class, 'index'])->name('admin.availability');
    Route::post('/availability', [AdminAvailabilityController::class, 'storeBlockedDate'])->name('admin.availability.store');
    Route::delete('/availability/{id}', [AdminAvailabilityController::class, 'destroy'])->name('admin.availability.delete');

    // 🔹 FIXED: Removed the duplicate "/admin" prefix from these URLs
    Route::get('/appointments/active', [AppointmentController::class, 'activeAppointments'])->name('admin.appointments.active');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('admin.appointments.complete');
});

// SUPER ADMIN SECURE ROUTES
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::delete('/appointments/{id}', [SuperAdminDashboardController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/users', [SuperAdminDashboardController::class, 'users'])->name('users');
    Route::post('/users', [SuperAdminDashboardController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [SuperAdminDashboardController::class, 'updateUser'])->name('users.update');

    // Availability & Schedule Settings
    Route::get('/availability', [SuperAdminDashboardController::class, 'availability'])->name('availability');
    Route::post('/availability', [SuperAdminDashboardController::class, 'storeBlockedDate'])->name('availability.store');
    Route::delete('/availability/{id}', [SuperAdminDashboardController::class, 'destroyBlockedDate'])->name('availability.destroy');
    Route::put('/appointments/{id}/reschedule', [SuperAdminDashboardController::class, 'reschedule'])->name('appointments.reschedule');
    Route::get('/appointments/export', [SuperAdminDashboardController::class, 'exportAppointments'])->name('appointments.export');
});

require __DIR__ . '/auth.php';