<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\ReportSubmissionController;
use App\Http\Controllers\WeeklyReportController;
use App\Http\Controllers\ReportTypeController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BarangayFileController;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Models\{WeeklyReport, MonthlyReport, QuarterlyReport, SemestralReport, AnnualReport};

// Public Routes
Route::get('/', function () {
    return view('welcome'); // Or any other welcome page
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// File Routes - Public access
Route::get('/files/{id}', [ReportController::class, 'downloadFile'])->name('barangay.files.download');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::put('/admin/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::get('/admin/confirm-deactivation/{id}', [AdminController::class, 'confirmDeactivation'])->name('admin.confirmDeactivation');

    Route::get('admin/create-report', [ReportTypeController::class, 'create'])->name('admin.create-report');
    Route::post('admin/create-report', [ReportTypeController::class, 'store'])->name('admin.store-report');

    // Route::get('admin/create-report/{id}/edit', [ReportTypeController::class, 'create'])->name('admin.edit-report'); // Uses create() to reuse the form
    // Route::put('admin/create-report/{id}', [ReportTypeController::class, 'update'])->name('admin.update-report');

    Route::delete('admin/create-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.delete-report');

    Route::get('/admin/view-submissions', [ReportController::class, 'index'])->name('view.submissions');
    Route::post('/admin/update-report/{id}', [ReportController::class, 'update'])->name('update.report');

    Route::post('admin/create-report/{id?}', [ReportTypeController::class, 'storeOrUpdate'])->name('admin.storeOrUpdate');
    Route::delete('admin/create-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.destroy-report');

    Route::get('admin/create-report', [ReportTypeController::class, 'index'])->name('admin.create-report');
    Route::post('admin/store-report', [ReportTypeController::class, 'store'])->name('admin.store-report');
    Route::put('admin/update-report/{id}', [ReportTypeController::class, 'update'])->name('admin.update-report');
    Route::delete('admin/destroy-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.destroy-report');

    // Remove or comment out any duplicate file routes
    // Route::get('/files/{filename}', function ($filename) { ... })->middleware('auth');
});

// Protected Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/barangay/submissions', [BarangayController::class, 'submissions'])->name('barangay.submissions');
    Route::get('/barangay/submissions', [BarangayController::class, 'submissions'])->name('barangay.submissions');
    Route::post('/barangay/submissions/store', [BarangayController::class, 'store'])->name('barangay.submissions.store');
    Route::get('/admin/dashboard', [ReportTypeController::class, 'index'])->name('admin.report_types.index');
    Route::post('/admin/report-types', [ReportTypeController::class, 'store'])->name('admin.report_types.store');

    Route::get('/barangay/submissions', [BarangayController::class, 'submissions'])->name('barangay.submissions');
    Route::post('/barangay/submissions/store', [BarangayController::class, 'store'])->name('barangay.submissions.store');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{id}/confirm-deactivation', [AdminController::class, 'confirmDeactivation']);

        // Report Types - Cleaned up routes
        Route::get('/create-report', [ReportTypeController::class, 'index'])->name('create-report');
        Route::post('/store-report', [ReportTypeController::class, 'store'])->name('store-report');
        Route::get('/edit-report/{id}', [ReportTypeController::class, 'edit'])->name('edit-report');
        Route::put('/update-report/{id}', [ReportTypeController::class, 'update'])->name('update-report');
        Route::delete('/destroy-report/{id}', [ReportTypeController::class, 'destroy'])->name('destroy-report');
    });

    // Barangay Routes
    Route::prefix('barangay')->name('barangay.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [BarangayController::class, 'dashboard'])->name('dashboard');

        // View Reports
        Route::get('/view-reports', [BarangayController::class, 'viewReports'])->name('view-reports');
        Route::get('/overdue-reports', [BarangayController::class, 'overdueReports'])->name('overdue-reports');
        Route::get('/submissions', [BarangayController::class, 'submissions'])->name('submissions');

        // File Management
        Route::get('/files/{id}', [ReportController::class, 'downloadFile'])->name('files.download');
        Route::delete('/files/{id}', [BarangayFileController::class, 'destroy'])->name('files.destroy');
    });
});

// Barangay routes
Route::post('barangay/files', [BarangayFileController::class, 'store'])->name('barangay.files.store');
Route::delete('barangay/files/{file}', [BarangayFileController::class, 'destroy'])->name('barangay.files.destroy');

Route::middleware(['auth'])->group(function () {
    Route::post('/barangay/submissions/resubmit', [BarangayController::class, 'resubmit'])->name('barangay.submissions.resubmit');
});

