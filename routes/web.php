<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\ReportSubmissionController;
<<<<<<< HEAD
use App\Http\Controllers\WeeklyReportController;
use App\Http\Controllers\ReportTypeController;
=======


use App\Http\Controllers\ReportController;
use App\Http\Controllers\BarangayFileController;

>>>>>>> parent of c0c49f2 (Seeded Users Table)

// Public Routes
Route::get('/', function () {
    return view('welcome'); // Or any other welcome page
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/barangay/submissions', [BarangayController::class, 'submissions'])->name('barangay.submissions');
    Route::get('/barangay/submissions', [BarangayController::class, 'submissions'])->name('barangay.submissions');
    Route::post('/barangay/submissions/store', [BarangayController::class, 'store'])->name('barangay.submissions.store');


    Route::get('/barangay/submissions', [BarangayController::class, 'submissions'])->name('barangay.submissions');
Route::post('/barangay/submissions/store', [BarangayController::class, 'store'])->name('barangay.submissions.store');



    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{id}/confirm-deactivation', [AdminController::class, 'confirmDeactivation']);

        // Report Management
        Route::get('/create-report', [ReportSubmissionController::class, 'create'])->name('create-report');
        Route::post('/store', [ReportSubmissionController::class, 'store'])->name('store');
        Route::get('/view-submissions', [ReportSubmissionController::class, 'viewSubmissions'])->name('view-submissions');
        Route::get('/submissions/{id}', [ReportSubmissionController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{id}/update', [ReportSubmissionController::class, 'updateStatus'])->name('submissions.update');

        // Report Types
        Route::get('/report-types', [ReportTypeController::class, 'index'])->name('report_types.index');
        Route::post('/report-types', [ReportTypeController::class, 'store'])->name('report_types.store');
    });

    // Barangay Routes
    Route::prefix('barangay')->name('barangay.')->group(function () {
        Route::post('/files', [BarangayController::class, 'storeFile'])->name('files.store');
        Route::get('/files/download/{id}', [BarangayController::class, 'downloadFile'])->name('files.download');
        Route::get('/files/view/{id}', [BarangayController::class, 'viewFile'])->name('files.view');
        Route::delete('/files/{id}', [BarangayController::class, 'deleteFile'])->name('files.destroy');

<<<<<<< HEAD
        // Weekly Report Submission
        Route::get('/submissions', [WeeklyReportController::class, 'create'])->name('submissions');
        Route::post('/submissions', [WeeklyReportController::class, 'store'])->name('submissions.store');
        Route::post('/submit-file/{id}', [ReportSubmissionController::class, 'submitFile'])->name('submit.file');
    });
=======
// Route for deleting (deactivating/reactivating) a user
Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');


Route::get('/admin/create-report', [ReportSubmissionController::class, 'create'])->name('admin.create');
Route::post('/admin/store', [ReportSubmissionController::class, 'store'])->name('admin.store');
Route::get('/admin/view-submissions', [ReportSubmissionController::class, 'viewSubmissions'])->name('admin.view-submissions');
Route::get('/admin/submissions/{id}', [ReportSubmissionController::class, 'show'])->name('admin.submissions.show');
Route::post('/admin/submissions/{id}/update', [ReportSubmissionController::class, 'updateStatus'])->name('admin.submissions.update');

Route::get('/barangay/submissions', [ReportSubmissionController::class, 'index'])->name('barangay.submissions');
// Route::post('/barangay/submissions/{id}/submit', [ReportSubmissionController::class, 'submitFile'])->name('barangay.submissions.submit');


 // Admin routes
 Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function() {
    Route::resource('reports', ReportController::class)->except(['show', 'edit', 'destroy']);
    Route::patch('reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.status');

    Route::get('admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');




>>>>>>> parent of c0c49f2 (Seeded Users Table)

    // Cluster Routes
    Route::prefix('cluster')->name('cluster.')->group(function () {
        Route::get('/dashboard', [ClusterController::class, 'index'])->name('index');
        Route::get('/create', [ClusterController::class, 'create'])->name('create');
        Route::post('/store', [ClusterController::class, 'store'])->name('store');
    });

    // Home Route
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});
<<<<<<< HEAD
=======

Route::post('/barangay/submissions/{id}/submit', [ReportSubmissionController::class, 'submitFile'])->name('barangay.submit');


Route::post('/barangay/submissions/{id}/submit', [ReportSubmissionController::class, 'submitFile'])->name('barangay.submissions.submit');



// Barangay routes
Route::post('barangay/files', [BarangayFileController::class, 'store'])->name('barangay.files.store');
Route::get('barangay/files/{file}/download', [BarangayFileController::class, 'download'])->name('barangay.files.download');
Route::delete('barangay/files/{file}', [BarangayFileController::class, 'destroy'])->name('barangay.files.destroy');









});

Route::prefix('barangay')->name('barangay.')->group(function () {
    Route::post('/submit-file/{id}', [ReportSubmissionController::class, 'submitFile'])->name('submit.file');
});




Route::get('/home', function () {
    return view('home');
})->middleware('auth'); // Protect the /home route
>>>>>>> parent of c0c49f2 (Seeded Users Table)
