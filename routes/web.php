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

// SUPER ADMIN SECURE ROUTES
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::delete('/appointments/{id}', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/users', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'users'])->name('users');
    Route::get('/users', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'users'])->name('users');
    Route::put('/users/{id}', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'updateUser'])->name('users.update');
    // NEW: Availability & Schedule Settings
    Route::get('/availability', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'availability'])->name('availability');
    Route::post('/availability', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'storeBlockedDate'])->name('availability.store');
    Route::delete('/availability/{id}', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'destroyBlockedDate'])->name('availability.destroy');
    Route::put('/appointments/{id}/reschedule', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'reschedule'])->name('appointments.reschedule');
    Route::get('/reports', [SuperAdminDashboardController::class, 'reports'])->name('reports');
    Route::get('/appointments/export', [SuperAdminDashboardController::class, 'exportAppointments'])->name('appointments.export');
});

require __DIR__ . '/auth.php';
