<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\SuperAdminController;

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

// COMPLAINT ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/complaint', [ComplaintController::class, 'create'])->name('complaint.create');
    Route::post('/complaint', [ComplaintController::class, 'store'])->name('complaint.store');
    Route::get('/my-complaints', [ComplaintController::class, 'index'])->name('complaint.index');
});

// ADMIN ROUTES
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/requests', [AdminController::class, 'appointments'])->name('admin.requests');
    Route::patch('/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/appointment/{id}/request-reschedule', [AdminController::class, 'requestReschedule'])->name('admin.appointment.reschedule');
    Route::patch('/appointment/{id}/reject', [AppointmentController::class, 'reject'])->name('admin.reject');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/report/pdf', [AdminController::class, 'downloadReportPdf'])->name('admin.report.pdf');
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('admin.complaints');
    Route::post('/complaints/{id}/resolve', [AdminController::class, 'resolveComplaint'])->name('admin.complaints.resolve');
});

require __DIR__ . '/auth.php';
