<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\ReportSubmissionController;
use App\Http\Controllers\WeeklyReportController;
use App\Http\Controllers\ReportTypeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BarangayFileController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ReportSubmissionsController;

// Public Routes
Route::get('/', function () {
    return view('welcome'); // Or any other welcome page
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ensure this route is defined only once
Route::get('/barangay/submissions', [AuthController::class, 'showSubmitReport'])->name('barangay.submissions');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::get('/admin/confirm-deactivation/{id}', [AdminController::class, 'confirmDeactivation'])->name('admin.confirmDeactivation');

    // Remove duplicate route definitions
    Route::get('/barangay/submit-report', [AuthController::class, 'showSubmitReport'])->name('barangay.submit-report');

    Route::get('admin/create-report', [ReportTypeController::class, 'create'])->name('admin.create-report');
    Route::post('admin/create-report', [ReportTypeController::class, 'store'])->name('admin.store-report');
    Route::delete('admin/create-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.delete-report');
    Route::post('admin/create-report/{id?}', [ReportTypeController::class, 'storeOrUpdate'])->name('admin.storeOrUpdate');
    Route::delete('admin/create-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.destroy-report');
    Route::get('admin/create-report', [ReportTypeController::class, 'index'])->name('admin.create-report');
    Route::post('admin/store-report', [ReportTypeController::class, 'store'])->name('admin.store-report');
    Route::put('admin/update-report/{id}', [ReportTypeController::class, 'update'])->name('admin.update-report');
    Route::delete('admin/destroy-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.destroy-report');

    Route::get('/files/{filename}', function ($filename) {
        $report = Report::where('file_path', 'reports/' . $filename)->where('user_id', Auth::id())->first();
        if (!$report) {
            abort(403, 'Unauthorized access.');
        }
        $path = storage_path("app/public/reports/{$filename}");
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }
        return Response::file($path);
    })->middleware('auth');

    Route::get('/admin/view-submissions', [ReportSubmissionsController::class, 'index'])->name('view.submissions');
    Route::post('/admin/update-report/{id}', [ReportSubmissionsController::class, 'update'])->name('update.report');

    Route::get('/barangay/submit-report', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/barangay/submit-report', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index'); // View page
});

// Home Route
Route::get('/home', function () {
    return view('home');
})->name('home');
