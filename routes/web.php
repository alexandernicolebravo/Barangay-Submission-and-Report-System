<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\BarangayController;




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



      // Display a list of the user's uploaded files
      Route::get('/barangay/dashboard', [BarangayController::class, 'index'])->name('barangay.index');

      // Upload a new file
      Route::post('/barangay/upload', [BarangayController::class, 'store'])->name('barangay.store');

      // Download a file
      Route::get('/barangay/download/{id}', [BarangayController::class, 'download'])->name('barangay.download');

      // View a file (image or PDF preview)
      Route::get('/barangay/view/{id}', [BarangayController::class, 'view'])->name('barangay.view');

      // Delete a file
      Route::delete('/barangay/delete/{id}', [BarangayController::class, 'destroy'])->name('barangay.destroy');


      Route::get('/cluster/dashboard', [ClusterController::class, 'index'])->name('cluster.index');
      Route::get('/cluster/create', [ClusterController::class, 'create'])->name('cluster.create');
      Route::post('/cluster/store', [ClusterController::class, 'store'])->name('cluster.store');


      Route::get('/cluster/store', function() {
        return "GET method hit for /cluster/store, should be a POST request";
    });





});



Route::get('/home', function () {
    return view('home');
})->middleware('auth'); // Protect the /home route
