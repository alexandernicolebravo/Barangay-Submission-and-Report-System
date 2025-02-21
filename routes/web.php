<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\ReportSubmissionController;


use App\Http\Controllers\ReportController;
use App\Http\Controllers\BarangayFileController;


Route::get('/', function () {
    return view('welcome'); // Or any other welcome page
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.store');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');



    Route::get('/barangay/dashboard', [BarangayController::class, 'index'])->name('barangay.dashboard');
    Route::post('/barangay/files', [BarangayController::class, 'storeFile'])->name('barangay.files.store');
    Route::get('/barangay/files/download/{id}', [BarangayController::class, 'downloadFile'])->name('barangay.files.download');
    Route::get('/barangay/files/view/{id}', [BarangayController::class, 'viewFile'])->name('barangay.files.view');
    Route::delete('/barangay/files/{id}', [BarangayController::class, 'deleteFile'])->name('barangay.files.destroy');


      Route::get('/cluster/dashboard', [ClusterController::class, 'index'])->name('cluster.index');
      Route::get('/cluster/create', [ClusterController::class, 'create'])->name('cluster.create');
      Route::post('/cluster/store', [ClusterController::class, 'store'])->name('cluster.store');


      Route::get('/cluster/store', function() {
        return "GET method hit for /cluster/store, should be a POST request";
    });

 // Route for displaying the confirmation message for deactivation
Route::get('/admin/users/{id}/confirm-deactivation', [AdminController::class, 'confirmDeactivation']);

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











});

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
