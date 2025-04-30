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
use App\Models\ReportFile;

// Public Routes
Route::get('/', function () {
    return view('welcome'); // Or any other welcome page
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::get('/admin/confirm-deactivation/{id}', [AdminController::class, 'confirmDeactivation'])->name('admin.confirmDeactivation');





    Route::get('admin/create-report', [ReportTypeController::class, 'create'])->name('admin.create-report');
    Route::post('admin/create-report', [ReportTypeController::class, 'store'])->name('admin.store-report');

    // Route::get('admin/create-report/{id}/edit', [ReportTypeController::class, 'create'])->name('admin.edit-report'); // Uses create() to reuse the form
    // Route::put('admin/create-report/{id}', [ReportTypeController::class, 'update'])->name('admin.update-report');

    Route::delete('admin/create-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.delete-report');


    Route::get('/admin/view-submissions', [ReportSubmissionsController::class, 'index'])->name('view.submissions');
    Route::post('/admin/update-report/{id}', [ReportSubmissionsController::class, 'update'])->name('update.report');



Route::post('admin/create-report/{id?}', [ReportTypeController::class, 'storeOrUpdate'])->name('admin.storeOrUpdate');
Route::delete('admin/create-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.destroy-report');


Route::get('admin/create-report', [ReportTypeController::class, 'index'])->name('admin.create-report');
Route::post('admin/store-report', [ReportTypeController::class, 'store'])->name('admin.store-report');
Route::put('admin/update-report/{id}', [ReportTypeController::class, 'update'])->name('admin.update-report');
Route::delete('admin/destroy-report/{id}', [ReportTypeController::class, 'destroy'])->name('admin.destroy-report');


Route::get('/files/{filename}', function ($filename) {

    $report = ReportFile::where('file_path', 'reports/' . $filename)->where('user_id', Auth::id())->first();

    if (!$report) {
        abort(403, 'Unauthorized access.');
    }

    $path = storage_path("app/public/reports/{$filename}");

    if (!file_exists($path)) {

        abort(404, 'File not found.');


        abort(404, 'File not found.');
    }



    return Response::file($path);
})->middleware('auth');





Route::get('/admin/view-submissions', [ReportSubmissionsController::class, 'index'])->name('view.submissions');
Route::post('/admin/update-report/{id}', [ReportSubmissionsController::class, 'update'])->name('update.report');




//     Route::get('/admin/create-report', [ReportTypeController::class, 'create'])->name('report_types.create');
//     Route::post('/admin/create-report', [ReportTypeController::class, 'store'])->name('report_types.store');
//     Route::delete('/admin/create-report/{id}', [ReportTypeController::class, 'destroy'])->name('report_types.destroy');


//     Route::get('/report-types/edit/{id}', [ReportTypeController::class, 'create'])->name('report_types.edit'); // For editing
// Route::put('/report-types/{id}', [ReportTypeController::class, 'update'])->name('report_types.update'); // Update function



    Route::get('/barangay/submit-report', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/barangay/submit-report', [ReportController::class, 'store'])->name('reports.store');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index'); // View page
    // Route::get('/reports/show', [ReportController::class, 'showReports'])->name('reports.show'); // AJAX fetching
    // Route::get('/barangay/create-report', [ReportController::class, 'showReports'])->name('reports.view');

    Route::get('/barangay/submit-report', [ReportController::class, 'showSubmitReport']);



    // Route::get('/admin/view-submissions', [ReportController::class, 'index'])->name('reports.index');
    // Route::put('/admin/view-submissions/{id}', [ReportController::class, 'update'])->name('reports.update');
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
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{id}/confirm-deactivation', [AdminController::class, 'confirmDeactivation']);



        // Report Types

    });

    // Barangay Routes
    Route::prefix('barangay')->name('barangay.')->group(function () {
        Route::post('/files', [BarangayController::class, 'storeFile'])->name('files.store');
        Route::get('/files/download/{id}', [BarangayController::class, 'downloadFile'])->name('files.download');
        Route::get('/files/view/{id}', [BarangayController::class, 'viewFile'])->name('files.view');
        Route::delete('/files/{id}', [BarangayController::class, 'deleteFile'])->name('files.destroy');

        // Weekly Report Submission
        Route::get('/submissions', [WeeklyReportController::class, 'create'])->name('submissions');
        Route::post('/submissions', [WeeklyReportController::class, 'store'])->name('submissions.store');

    });




// Route::post('/barangay/submissions/{id}/submit', [ReportSubmissionController::class, 'submitFile'])->name('barangay.submissions.submit');


 // Admin routes
 Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function() {
    // Route::resource('reports', ReportController::class)->except(['show', 'edit', 'destroy']);
    // Route::patch('reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.status');

    // Route::get('admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');






    // Home Route
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});




// Barangay routes
Route::post('barangay/files', [BarangayFileController::class, 'store'])->name('barangay.files.store');
Route::get('barangay/files/{file}/download', [BarangayFileController::class, 'download'])->name('barangay.files.download');
Route::delete('barangay/files/{file}', [BarangayFileController::class, 'destroy'])->name('barangay.files.destroy');









});









