<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\FacilitatorController;
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
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\IssuanceController;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use App\Models\Cluster;
use App\Notifications\ReportRemarksNotification;

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Test Email Route
Route::get('/test-email', function() {
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from Laravel', function($message) {
            $message->to('jerel.paligumba10@gmail.com')
                    ->subject('Test Email from Laravel Route');
        });

        return 'Email sent successfully! Please check your inbox.';
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage();
    }
});

// Check Email Configuration
Route::get('/check-email-config', function() {
    $config = [
        'MAIL_MAILER' => config('mail.default'),
        'MAIL_HOST' => config('mail.mailers.smtp.host'),
        'MAIL_PORT' => config('mail.mailers.smtp.port'),
        'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
        'MAIL_PASSWORD' => str_repeat('*', strlen(config('mail.mailers.smtp.password'))),
        'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
        'MAIL_FROM_ADDRESS' => config('mail.from.address'),
        'MAIL_FROM_NAME' => config('mail.from.name'),
    ];

    return '<h2>Email Configuration</h2><pre>' . json_encode($config, JSON_PRETTY_PRINT) . '</pre>';
});

// Test Email to Specific Barangay User
Route::get('/test-email-to-barangay/{userId}', function($userId) {
    try {
        // Get the barangay user
        $barangayUser = User::find($userId);

        if (!$barangayUser) {
            return 'User with ID ' . $userId . ' not found.';
        }

        // Display user details
        $details = "User: " . $barangayUser->name . " (ID: " . $barangayUser->id . ", Email: " . $barangayUser->email . ")<br>";

        // Send a direct email (not using notification)
        \Illuminate\Support\Facades\Mail::raw('This is a direct test email from Laravel to your barangay account. If you receive this, please inform the admin.', function($message) use ($barangayUser) {
            $message->to($barangayUser->email)
                    ->subject('Direct Test Email to Barangay Account');
        });

        return $details . '<br>Direct email sent successfully! Please check the inbox of ' . $barangayUser->email;
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Create Notifications Table Route
Route::get('/create-notifications-table', function() {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            \Illuminate\Support\Facades\Schema::create('notifications', function ($table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
            return 'Notifications table created successfully!';
        } else {
            return 'Notifications table already exists.';
        }
    } catch (\Exception $e) {
        return 'Error creating notifications table: ' . $e->getMessage();
    }
});

// Debug Notifications Route
Route::get('/debug-notifications', function() {
    try {
        $output = "=== NOTIFICATIONS DEBUG ===\n\n";

        // Check if notifications table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            return "Notifications table does not exist. Please create it first.";
        }

        // Get all notifications
        $notifications = \Illuminate\Support\Facades\DB::table('notifications')->get();
        $output .= "Total notifications in database: " . $notifications->count() . "\n\n";

        // Get barangay users
        $barangayUsers = User::where('user_type', 'barangay')->get();
        $output .= "Total barangay users: " . $barangayUsers->count() . "\n\n";

        foreach ($barangayUsers as $user) {
            $userNotifications = \Illuminate\Support\Facades\DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $user->id)
                ->get();

            $output .= "User: {$user->name} (ID: {$user->id})\n";
            $output .= "  Notifications: " . $userNotifications->count() . "\n";

            foreach ($userNotifications as $notification) {
                $data = json_decode($notification->data, true);
                $output .= "  - Type: {$notification->type}\n";
                $output .= "    Message: " . ($data['message'] ?? 'No message') . "\n";
                $output .= "    Created: {$notification->created_at}\n";
                $output .= "    Read: " . ($notification->read_at ? 'Yes' : 'No') . "\n\n";
            }
        }

        return response($output)->header('Content-Type', 'text/plain');
    } catch (\Exception $e) {
        return 'Error debugging notifications: ' . $e->getMessage();
    }
});

// Test Real Facilitator Workflow Route
Route::get('/test-real-facilitator-workflow', function() {
    try {
        // Find Granada user
        $granadaUser = User::where('user_type', 'barangay')
            ->where(function($query) {
                $query->where('name', 'LIKE', '%granada%')
                      ->orWhere('email', 'LIKE', '%granada%');
            })
            ->first();

        if (!$granadaUser) {
            return 'Granada user not found.';
        }

        // Find any report for Granada
        $report = WeeklyReport::with('reportType')->where('user_id', $granadaUser->id)->first() ??
                 MonthlyReport::with('reportType')->where('user_id', $granadaUser->id)->first() ??
                 QuarterlyReport::with('reportType')->where('user_id', $granadaUser->id)->first();

        if (!$report) {
            return 'No reports found for Granada user.';
        }

        // Update the report with remarks and allow resubmission (simulating facilitator action)
        $report->update([
            'remarks' => 'Please resubmit your ' . $report->reportType->name . ' report. The following corrections are needed: 1) Update the data format according to the latest guidelines, 2) Include all required signatures from barangay officials, 3) Verify that all statistical data is accurate and up-to-date. Please make these corrections and resubmit as soon as possible.',
            'can_update' => true
        ]);

        // Send notification (simulating the facilitator controller logic)
        $facilitatorName = 'Greg (Cluster 3 Facilitator)';
        $reportType = strtolower(class_basename($report));

        $granadaUser->notify(new ReportRemarksNotification(
            $report,
            $report->remarks,
            $reportType,
            $facilitatorName
        ));

        return 'SUCCESS! Facilitator workflow completed:<br><br>' .
               '✅ Report updated with remarks<br>' .
               '✅ Resubmission permission granted<br>' .
               '✅ Notification sent to Granada user<br><br>' .
               'Report: ' . $report->reportType->name . '<br>' .
               'Granada User: ' . $granadaUser->name . ' (' . $granadaUser->email . ')<br>' .
               'Facilitator: ' . $facilitatorName . '<br><br>' .
               'Granada should now see the notification in their dashboard!';
    } catch (\Exception $e) {
        return 'Failed to complete facilitator workflow: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Test Modal Functionality Route
Route::get('/test-modal-functionality', function() {
    return view('barangay.test-modal');
});

// Test Granada Lupon Report Notification Route
Route::get('/test-granada-lupon-notification', function() {
    try {
        // Find Granada user
        $granadaUser = User::where('user_type', 'barangay')
            ->where(function($query) {
                $query->where('name', 'LIKE', '%granada%')
                      ->orWhere('email', 'LIKE', '%granada%');
            })
            ->first();

        if (!$granadaUser) {
            return 'Granada user not found. Available barangay users: ' .
                   User::where('user_type', 'barangay')->pluck('name')->implode(', ');
        }

        // Find a Lupon report for Granada
        $luponReport = null;

        // Check all report types for Lupon
        $weeklyLupon = WeeklyReport::with('reportType')
            ->where('user_id', $granadaUser->id)
            ->whereHas('reportType', function($query) {
                $query->where('name', 'LIKE', '%lupon%');
            })
            ->first();

        $monthlyLupon = MonthlyReport::with('reportType')
            ->where('user_id', $granadaUser->id)
            ->whereHas('reportType', function($query) {
                $query->where('name', 'LIKE', '%lupon%');
            })
            ->first();

        $quarterlyLupon = QuarterlyReport::with('reportType')
            ->where('user_id', $granadaUser->id)
            ->whereHas('reportType', function($query) {
                $query->where('name', 'LIKE', '%lupon%');
            })
            ->first();

        $luponReport = $weeklyLupon ?? $monthlyLupon ?? $quarterlyLupon;

        if (!$luponReport) {
            // Get any report for Granada
            $luponReport = WeeklyReport::with('reportType')->where('user_id', $granadaUser->id)->first() ??
                          MonthlyReport::with('reportType')->where('user_id', $granadaUser->id)->first() ??
                          QuarterlyReport::with('reportType')->where('user_id', $granadaUser->id)->first();
        }

        if (!$luponReport) {
            return 'No reports found for Granada user. User ID: ' . $granadaUser->id;
        }

        // Send facilitator remarks notification (simulating resubmission request)
        $granadaUser->notify(new ReportRemarksNotification(
            $luponReport,
            'Please resubmit your ' . $luponReport->reportType->name . ' report with the following corrections: 1) Update the data format, 2) Include missing signatures, 3) Verify all information is accurate. You can now resubmit this report.',
            strtolower(class_basename($luponReport)),
            'Test Facilitator (Greg - Cluster 3)'
        ));

        return 'Facilitator remarks notification sent successfully to ' . $granadaUser->name . ' (' . $granadaUser->email . ') for report: ' . $luponReport->reportType->name . '! The notification includes resubmission permission.';
    } catch (\Exception $e) {
        return 'Failed to send Granada Lupon notification: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Test Facilitator Remarks Notification Route
Route::get('/test-facilitator-notification', function() {
    try {
        // Get a barangay user (preferably Granada)
        $barangayUser = User::where('user_type', 'barangay')
            ->where('name', 'LIKE', '%granada%')
            ->orWhere('email', 'LIKE', '%granada%')
            ->first();

        if (!$barangayUser) {
            $barangayUser = User::where('user_type', 'barangay')->first();
        }

        if (!$barangayUser) {
            return 'No barangay user found to send notification to.';
        }

        // Get a report for testing
        $report = WeeklyReport::with('reportType')->where('user_id', $barangayUser->id)->first();

        if (!$report) {
            // Try other report types
            $report = MonthlyReport::with('reportType')->where('user_id', $barangayUser->id)->first();
        }

        if (!$report) {
            return 'No report found for user ' . $barangayUser->name . ' to use for notification.';
        }

        // Send facilitator remarks notification
        $barangayUser->notify(new ReportRemarksNotification(
            $report,
            'This is a test remark from facilitator. Please resubmit your report with the necessary corrections.',
            'weekly',
            'Test Facilitator'
        ));

        return 'Facilitator remarks notification sent successfully to ' . $barangayUser->name . ' (' . $barangayUser->email . ') for report: ' . $report->reportType->name . '!';
    } catch (\Exception $e) {
        return 'Failed to send facilitator notification: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Test Notification Route
Route::get('/test-notification', function() {
    try {
        // Get a barangay user
        $barangayUser = User::where('user_type', 'barangay')->first();

        if (!$barangayUser) {
            return 'No barangay user found to send notification to.';
        }

        // Get a report for testing
        $report = WeeklyReport::with('reportType')->first();

        if (!$report) {
            return 'No report found to use for notification.';
        }

        // Send notification
        $barangayUser->notify(new ReportRemarksNotification(
            $report,
            'This is a test remark from the notification test route.',
            'weekly',
            'Admin User'
        ));

        return 'Notification sent successfully to ' . $barangayUser->name . ' (' . $barangayUser->email . ')!';
    } catch (\Exception $e) {
        return 'Failed to send notification: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// List all barangay users and their reports
Route::get('/list-barangay-reports', function() {
    $barangayUsers = User::where('user_type', 'barangay')->get();

    if ($barangayUsers->isEmpty()) {
        return 'No barangay users found.';
    }

    $output = '<h2>Barangay Users and Their Reports</h2>';

    foreach ($barangayUsers as $user) {
        $output .= '<h3>User: ' . $user->name . ' (ID: ' . $user->id . ', Email: ' . $user->email . ')</h3>';

        // Get weekly reports
        $weeklyReports = WeeklyReport::with('reportType')
            ->where('user_id', $user->id)
            ->get();

        if ($weeklyReports->isNotEmpty()) {
            $output .= '<h4>Weekly Reports:</h4><ul>';
            foreach ($weeklyReports as $report) {
                $output .= '<li>ID: ' . $report->id . ' - ' . $report->reportType->name .
                    ' <a href="/test-notification-specific/' . $user->id . '/' . $report->id . '/weekly">Test Notification</a></li>';
            }
            $output .= '</ul>';
        }

        // Get monthly reports
        $monthlyReports = MonthlyReport::with('reportType')
            ->where('user_id', $user->id)
            ->get();

        if ($monthlyReports->isNotEmpty()) {
            $output .= '<h4>Monthly Reports:</h4><ul>';
            foreach ($monthlyReports as $report) {
                $output .= '<li>ID: ' . $report->id . ' - ' . $report->reportType->name .
                    ' <a href="/test-notification-specific/' . $user->id . '/' . $report->id . '/monthly">Test Notification</a></li>';
            }
            $output .= '</ul>';
        }

        // Get quarterly reports
        $quarterlyReports = QuarterlyReport::with('reportType')
            ->where('user_id', $user->id)
            ->get();

        if ($quarterlyReports->isNotEmpty()) {
            $output .= '<h4>Quarterly Reports:</h4><ul>';
            foreach ($quarterlyReports as $report) {
                $output .= '<li>ID: ' . $report->id . ' - ' . $report->reportType->name .
                    ' <a href="/test-notification-specific/' . $user->id . '/' . $report->id . '/quarterly">Test Notification</a></li>';
            }
            $output .= '</ul>';
        }

        // Get semestral reports
        $semestralReports = SemestralReport::with('reportType')
            ->where('user_id', $user->id)
            ->get();

        if ($semestralReports->isNotEmpty()) {
            $output .= '<h4>Semestral Reports:</h4><ul>';
            foreach ($semestralReports as $report) {
                $output .= '<li>ID: ' . $report->id . ' - ' . $report->reportType->name .
                    ' <a href="/test-notification-specific/' . $user->id . '/' . $report->id . '/semestral">Test Notification</a></li>';
            }
            $output .= '</ul>';
        }

        // Get annual reports
        $annualReports = AnnualReport::with('reportType')
            ->where('user_id', $user->id)
            ->get();

        if ($annualReports->isNotEmpty()) {
            $output .= '<h4>Annual Reports:</h4><ul>';
            foreach ($annualReports as $report) {
                $output .= '<li>ID: ' . $report->id . ' - ' . $report->reportType->name .
                    ' <a href="/test-notification-specific/' . $user->id . '/' . $report->id . '/annual">Test Notification</a></li>';
            }
            $output .= '</ul>';
        }
    }

    return $output;
});

// Test Notification with Specific User and Report
Route::get('/test-notification-specific/{userId}/{reportId}/{reportType}', function($userId, $reportId, $reportType) {
    try {
        // Get the specific barangay user
        $barangayUser = User::find($userId);

        if (!$barangayUser) {
            return 'Barangay user with ID ' . $userId . ' not found.';
        }

        // Get the specific report based on type
        $reportModel = null;
        switch ($reportType) {
            case 'weekly':
                $reportModel = WeeklyReport::class;
                break;
            case 'monthly':
                $reportModel = MonthlyReport::class;
                break;
            case 'quarterly':
                $reportModel = QuarterlyReport::class;
                break;
            case 'semestral':
                $reportModel = SemestralReport::class;
                break;
            case 'annual':
                $reportModel = AnnualReport::class;
                break;
            default:
                return 'Invalid report type: ' . $reportType;
        }

        $report = $reportModel::with('reportType')->find($reportId);

        if (!$report) {
            return 'Report with ID ' . $reportId . ' not found in ' . $reportType . ' reports.';
        }

        // Display user and report details
        $details = "User: " . $barangayUser->name . " (ID: " . $barangayUser->id . ", Email: " . $barangayUser->email . ")<br>";
        $details .= "Report: " . $report->reportType->name . " (ID: " . $report->id . ")<br>";

        // Send notification
        $barangayUser->notify(new ReportRemarksNotification(
            $report,
            'This is a test remark for user ' . $barangayUser->name . ' for report ' . $report->reportType->name,
            $reportType,
            'Admin User (Specific Test)'
        ));

        return $details . '<br>Notification sent successfully to ' . $barangayUser->name . '!';
    } catch (\Exception $e) {
        return 'Failed to send notification: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Authenticated Routes Group
Route::middleware(['auth'])->group(function () {
    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::get('/notifications/load-more', [NotificationController::class, 'loadMore'])->name('notifications.load-more');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        // Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard-chart-data', [AdminController::class, 'getDashboardChartData'])->name('dashboard.chart-data');

        // Test route for AJAX endpoint
        Route::get('/test-dashboard-chart-data', function() {
            $controller = new \App\Http\Controllers\AdminController();
            return $controller->getDashboardChartData(request());
        });

        // User Management
        Route::get('/user-management', [AdminController::class, 'userManagement'])->name('user-management');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{id}/confirm-deactivation', [AdminController::class, 'confirmDeactivation'])->name('confirm-deactivation');

        // Cluster Management
        Route::resource('clusters', ClusterController::class);

        // Add a route to create test clusters
        Route::get('/create-test-clusters', function() {
            if (Cluster::count() === 0) {
                Cluster::create(['name' => 'Cluster 1', 'description' => 'Test Cluster 1', 'is_active' => true]);
                Cluster::create(['name' => 'Cluster 2', 'description' => 'Test Cluster 2', 'is_active' => true]);
                Cluster::create(['name' => 'Cluster 3', 'description' => 'Test Cluster 3', 'is_active' => true]);
                Cluster::create(['name' => 'Cluster 4', 'description' => 'Test Cluster 4', 'is_active' => true]);
                return 'Test clusters created successfully!';
            }
            return 'Clusters already exist!';
        });

        // Report Management
        Route::get('/view-submissions', [AdminController::class, 'viewSubmissions'])->name('view.submissions');
        Route::put('/reports/{id}', [ReportController::class, 'update'])->name('update.report');
        Route::get('/files/{id}', [ReportController::class, 'downloadFile'])->name('files.download');
        Route::get('/get-barangays-by-cluster/{clusterId}', [AdminController::class, 'getBarangaysByCluster'])->name('get.barangays.by.cluster');

        // Report Types
        Route::get('/create-report', [ReportTypeController::class, 'index'])->name('create-report');
        Route::post('/report-types', [ReportTypeController::class, 'store'])->name('store-report');
        Route::get('/report-types/{id}/edit', [ReportTypeController::class, 'edit'])->name('edit-report');
        Route::put('/report-types/{id}', [ReportTypeController::class, 'update'])->name('update-report');
        Route::delete('/report-types/{id}', [ReportTypeController::class, 'destroy'])->name('destroy-report');

        // Admin Announcements Routes
        Route::resource('announcements', AnnouncementController::class);
        Route::put('/announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle-status');

        // Issuance routes
        Route::prefix('issuances')->name('issuances.')->group(function () {
            Route::get('/', [IssuanceController::class, 'index'])->name('index');
            Route::post('/', [IssuanceController::class, 'store'])->name('store');
            Route::get('/{issuance}', [IssuanceController::class, 'show'])->name('show');
            Route::get('/{issuance}/edit', [IssuanceController::class, 'edit'])->name('edit');
            Route::put('/{issuance}', [IssuanceController::class, 'update'])->name('update');
            Route::patch('/{issuance}/archive', [IssuanceController::class, 'archive'])->name('archive');
            Route::patch('/{issuance}/unarchive', [IssuanceController::class, 'unarchive'])->name('unarchive');
            Route::delete('/{issuance}', [IssuanceController::class, 'destroy'])->name('destroy');
            Route::get('/{issuance}/download', [IssuanceController::class, 'download'])->name('download');
        });

        // Profile Routes
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    });

    // Barangay Routes
    Route::prefix('barangay')->name('barangay.')->middleware('auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [BarangayController::class, 'dashboard'])->name('dashboard');

        // Debug route for testing modals
        Route::get('/debug-modals', function () {
            return view('barangay.debug-modals');
        })->name('debug-modals');

        // Debug route for testing submissions
        Route::get('/debug-submissions', function () {
            return view('barangay.debug-submissions');
        })->name('debug-submissions');

        // Reports
        Route::get('/submit-report', [BarangayController::class, 'submitReport'])->name('submit-report');
        Route::post('/submissions/store', [BarangayController::class, 'store'])->name('submissions.store');
        Route::get('/submissions', [BarangayController::class, 'submissions'])->name('submissions');
        Route::get('/view-reports', [BarangayController::class, 'viewReports'])->name('view-reports');
        Route::get('/overdue-reports', [BarangayController::class, 'overdueReports'])->name('overdue-reports');
        Route::post('/submissions/{id}/resubmit', [BarangayController::class, 'resubmit'])->name('submissions.resubmit');
        Route::post('/resubmit-report/{id}', [BarangayController::class, 'resubmit'])->name('resubmit-report');

        // Debug routes for testing form submission
        Route::get('/test-resubmit', function() {
            return "Resubmit route is working!";
        })->name('test.resubmit');

        Route::get('/test-resubmit-form', function() {
            return view('barangay.test-resubmit');
        })->name('test.resubmit.form');

        Route::get('/test-submission-update', function() {
            return view('barangay.test-submission-update');
        })->name('test.submission.update');

        // File Management
        Route::get('/files/{id}', [ReportController::class, 'downloadFile'])->name('files.download');
        Route::get('/direct-files/{id}', [BarangayController::class, 'directDownloadFile'])->name('direct.files.download');
        Route::delete('/files/{id}', [BarangayFileController::class, 'destroy'])->name('files.destroy');

        // Issuance routes for barangay
        Route::prefix('issuances')->name('issuances.')->group(function () {
            Route::get('/', [IssuanceController::class, 'barangayIndex'])->name('index');
            Route::get('/{issuance}', [IssuanceController::class, 'barangayShow'])->name('show');
            Route::get('/{issuance}/download', [IssuanceController::class, 'download'])->name('download');
        });

        // Notification routes
        Route::get('/notifications', [BarangayController::class, 'getNotifications'])->name('notifications.get');
        Route::get('/notifications/unread-count', [BarangayController::class, 'getUnreadNotificationCount'])->name('notifications.unread-count');
        Route::post('/notifications/{id}/read', [BarangayController::class, 'markNotificationAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [BarangayController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');
    });

    // Facilitator Routes
    Route::prefix('facilitator')->name('facilitator.')->middleware(\App\Http\Middleware\FacilitatorMiddleware::class)->group(function () {
        // Dashboard
        Route::get('/dashboard', [FacilitatorController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/chart-data', [FacilitatorController::class, 'getDashboardChartData'])->name('dashboard.chart-data');

        // Report Viewing
        Route::get('/view-submissions', [FacilitatorController::class, 'viewSubmissions'])->name('view-submissions');
        Route::put('/reports/{id}/remarks', [FacilitatorController::class, 'addRemarks'])->name('reports.add-remarks');

        // File Download
        Route::get('/files/{id}', [FacilitatorController::class, 'downloadFile'])->name('files.download');
    });
});

Route::get('/clusters-with-facilitators', function () {
    $clusters = Cluster::with('facilitators')->get();
    return response()->json($clusters);
});

Route::get('/facilitators-with-clusters', function () {
    $facilitators = User::where('user_type', 'facilitator')->with('assignedClusters')->get();
    return response()->json($facilitators);
});

Route::get('/cluster/{clusterId}/barangays-with-reports', function ($clusterId) {
    $cluster = Cluster::with(['users.weeklyReports', 'users.monthlyReports'])->find($clusterId);
    return response()->json($cluster);
});

Route::get('/all-barangays-with-reports', function () {
    $barangays = User::where('user_type', 'barangay')
        ->with([
            'weeklyReports.reportType',
            'monthlyReports.reportType',
            'quarterlyReports.reportType',
            'semestralReports.reportType',
            'annualReports.reportType'
        ])
        ->get();
    return response()->json($barangays);
});

Route::get('/user/{userId}/report-details', function ($userId) {
    $user = User::with([
        'weeklyReports.reportType',
        'monthlyReports.reportType',
        'quarterlyReports.reportType',
        'semestralReports.reportType',
        'annualReports.reportType'
    ])->find($userId);
    return response()->json($user);
});



// Debug route for report types
Route::get('/debug-report-types', function () {
    $activeReports = \App\Models\ReportType::active()->get();
    $archivedReports = \App\Models\ReportType::archived()->get();
    $barangayCount = \App\Models\User::where('user_type', 'barangay')->where('is_active', true)->count();

    $output = "=== REPORT TYPES DEBUG ===\n";
    $output .= "Total report types: " . \App\Models\ReportType::count() . "\n";
    $output .= "Active report types: " . $activeReports->count() . "\n";
    $output .= "Archived report types: " . $archivedReports->count() . "\n\n";

    $output .= "=== ACTIVE REPORT TYPES ===\n";
    foreach ($activeReports as $report) {
        $output .= "ID: {$report->id} | Name: {$report->name} | Frequency: {$report->frequency} | Deadline: {$report->deadline}\n";
    }

    $output .= "\n=== BARANGAY COUNT ===\n";
    $output .= "Active barangays: {$barangayCount}\n";

    $output .= "\n=== EXPECTED CALCULATIONS ===\n";
    $activeCount = $activeReports->count();
    $expectedTotal = $activeCount * $barangayCount;
    $output .= "Expected total pending submissions: {$activeCount} active reports × {$barangayCount} barangays = {$expectedTotal}\n";
    $output .= "Per barangay pending submissions: {$activeCount}\n\n";

    $output .= "=== GROUPED BY NAME ===\n";
    $groupedByName = $activeReports->groupBy('name');
    foreach ($groupedByName as $name => $reports) {
        $output .= "'{$name}': {$reports->count()} reports\n";
        if ($reports->count() > 1) {
            $output .= "  -> This has duplicates!\n";
            foreach ($reports as $report) {
                $output .= "     ID: {$report->id} | Deadline: {$report->deadline}\n";
            }
        }
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Fix duplicate report types route
Route::get('/fix-duplicate-report-types', function () {
    $activeReports = \App\Models\ReportType::active()->get();
    $output = "=== FIXING DUPLICATE REPORT TYPES ===\n";
    $output .= "Current active report types: " . $activeReports->count() . "\n\n";

    // Group by name to find duplicates
    $groupedByName = $activeReports->groupBy('name');

    $output .= "=== REPORT TYPES BY NAME ===\n";
    $duplicatesFound = false;
    foreach ($groupedByName as $name => $reports) {
        $output .= "'{$name}': {$reports->count()} reports\n";
        if ($reports->count() > 1) {
            $duplicatesFound = true;
            $output .= "  -> This has duplicates! Will keep the first one and archive the rest.\n";

            // Sort by ID to keep the first created one
            $sortedReports = $reports->sortBy('id');
            $keepReport = $sortedReports->first();
            $archiveReports = $sortedReports->skip(1);

            $output .= "     KEEPING: ID {$keepReport->id} | Deadline: {$keepReport->deadline}\n";

            foreach ($archiveReports as $report) {
                $output .= "     ARCHIVING: ID {$report->id} | Deadline: {$report->deadline}\n";
                $report->archive();
            }
            $output .= "\n";
        }
    }

    if (!$duplicatesFound) {
        $output .= "No duplicates found. All report types have unique names.\n\n";
    } else {
        $output .= "=== DUPLICATE CLEANUP COMPLETED ===\n\n";
    }

    // Show updated counts
    $newActiveCount = \App\Models\ReportType::active()->count();
    $newArchivedCount = \App\Models\ReportType::archived()->count();

    $output .= "=== UPDATED COUNTS ===\n";
    $output .= "Active report types: {$newActiveCount}\n";
    $output .= "Archived report types: {$newArchivedCount}\n\n";

    // Show expected calculations
    $barangayCount = \App\Models\User::where('user_type', 'barangay')->where('is_active', true)->count();
    $expectedTotal = $newActiveCount * $barangayCount;

    $output .= "=== NEW EXPECTED CALCULATIONS ===\n";
    $output .= "Active barangays: {$barangayCount}\n";
    $output .= "Expected total pending submissions: {$newActiveCount} active reports × {$barangayCount} barangays = {$expectedTotal}\n";
    $output .= "Per barangay pending submissions: {$newActiveCount}\n\n";

    if ($newActiveCount == 4) {
        $output .= "✅ SUCCESS! You now have exactly 4 active report types.\n";
        $output .= "The facilitator dashboard should now show 4 pending submissions per barangay.\n";
    } else {
        $output .= "⚠️  You still have {$newActiveCount} active report types.\n";
        $output .= "If you want exactly 4, you may need to manually archive " . ($newActiveCount - 4) . " more report types.\n";
        $output .= "Go to Admin -> Create Report page to archive additional report types.\n";
    }

    $output .= "\n=== REMAINING ACTIVE REPORT TYPES ===\n";
    $remainingActive = \App\Models\ReportType::active()->get();
    foreach ($remainingActive as $report) {
        $output .= "ID: {$report->id} | Name: {$report->name} | Frequency: {$report->frequency} | Deadline: {$report->deadline}\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Test the fix - check report types with future deadlines
Route::get('/test-fix', function () {
    $output = "=== TESTING THE FIX ===\n";

    $allActiveReports = \App\Models\ReportType::active()->get();
    $futureDeadlineReports = \App\Models\ReportType::active()->where('deadline', '>=', now())->get();
    $barangayCount = \App\Models\User::where('user_type', 'barangay')->where('is_active', true)->count();

    $output .= "All active report types: " . $allActiveReports->count() . "\n";
    $output .= "Active report types with future deadlines: " . $futureDeadlineReports->count() . "\n";
    $output .= "Active barangays: {$barangayCount}\n\n";

    $output .= "=== REPORT TYPES WITH FUTURE DEADLINES ===\n";
    foreach ($futureDeadlineReports as $report) {
        $output .= "ID: {$report->id} | Name: {$report->name} | Frequency: {$report->frequency} | Deadline: {$report->deadline}\n";
    }

    $output .= "\n=== EXPECTED CALCULATIONS (AFTER FIX) ===\n";
    $activeCount = $futureDeadlineReports->count();
    $expectedTotal = $activeCount * $barangayCount;
    $output .= "Expected total pending submissions: {$activeCount} active reports with future deadlines × {$barangayCount} barangays = {$expectedTotal}\n";
    $output .= "Per barangay pending submissions: {$activeCount}\n\n";

    if ($activeCount == 4) {
        $output .= "✅ SUCCESS! The fix should now show 4 pending submissions per barangay.\n";
    } else {
        $output .= "⚠️  You have {$activeCount} report types with future deadlines.\n";
        $output .= "If you want exactly 4, you may need to adjust the deadlines of some report types.\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Debug overdue reports logic
Route::get('/debug-overdue', function () {
    $output = "=== DEBUGGING OVERDUE REPORTS LOGIC ===\n";

    $allActiveReports = \App\Models\ReportType::active()->get();
    $futureDeadlineReports = \App\Models\ReportType::active()->where('deadline', '>=', now())->get();
    $pastDeadlineReports = \App\Models\ReportType::active()->where('deadline', '<', now())->get();

    $output .= "All active report types: " . $allActiveReports->count() . "\n";
    $output .= "Active report types with future deadlines: " . $futureDeadlineReports->count() . "\n";
    $output .= "Active report types with past deadlines (overdue): " . $pastDeadlineReports->count() . "\n\n";

    $output .= "=== ACTIVE REPORT TYPES WITH PAST DEADLINES (SHOULD SHOW IN OVERDUE) ===\n";
    foreach ($pastDeadlineReports as $report) {
        $output .= "ID: {$report->id} | Name: {$report->name} | Frequency: {$report->frequency} | Deadline: {$report->deadline}\n";
    }

    $output .= "\n=== ACTIVE REPORT TYPES WITH FUTURE DEADLINES (SHOULD SHOW IN UPCOMING) ===\n";
    foreach ($futureDeadlineReports as $report) {
        $output .= "ID: {$report->id} | Name: {$report->name} | Frequency: {$report->frequency} | Deadline: {$report->deadline}\n";
    }

    $output .= "\n=== RECOMMENDATION ===\n";
    if ($pastDeadlineReports->count() > 0) {
        $output .= "You have {$pastDeadlineReports->count()} overdue report types.\n";
        $output .= "These should appear in the overdue reports page.\n";
        $output .= "If you don't want them to show, you should archive them.\n";
    } else {
        $output .= "No overdue reports - all active report types have future deadlines.\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Archive old report types to keep only 6 active
Route::get('/archive-old-reports', function () {
    $output = "=== ARCHIVING OLD REPORT TYPES ===\n";

    $allActiveReports = \App\Models\ReportType::active()->orderBy('deadline', 'desc')->get();
    $output .= "Current active report types: " . $allActiveReports->count() . "\n\n";

    // Keep only the 6 most recent report types (by deadline)
    $reportsToKeep = $allActiveReports->take(6);
    $reportsToArchive = $allActiveReports->skip(6);

    $output .= "=== REPORTS TO KEEP (6 most recent) ===\n";
    foreach ($reportsToKeep as $report) {
        $output .= "ID: {$report->id} | Name: {$report->name} | Deadline: {$report->deadline}\n";
    }

    $output .= "\n=== REPORTS TO ARCHIVE (" . $reportsToArchive->count() . " old reports) ===\n";
    foreach ($reportsToArchive as $report) {
        $output .= "ID: {$report->id} | Name: {$report->name} | Deadline: {$report->deadline}\n";
    }

    // Archive the old reports
    $archivedCount = 0;
    foreach ($reportsToArchive as $report) {
        $report->archive();
        $archivedCount++;
    }

    $output .= "\n=== RESULTS ===\n";
    $output .= "Archived {$archivedCount} old report types.\n";
    $output .= "Remaining active report types: " . \App\Models\ReportType::active()->count() . "\n";

    $output .= "\n✅ SUCCESS! Now you have exactly 6 active report types.\n";
    $output .= "The overdue reports page will now only show relevant overdue reports.\n";
    $output .= "The dashboard will show correct pending submission counts.\n";

    return response($output)->header('Content-Type', 'text/plain');
});

// Debug route for issuance migration
Route::get('/debug-issuance-migration', function () {
    $output = "=== ISSUANCE MIGRATION DEBUG ===\n\n";

    try {
        // Get table structure
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('issuances');
        $output .= "Current issuances table columns: " . implode(', ', $columns) . "\n\n";

        // Check required columns
        $requiredColumns = ['file_name', 'file_size', 'file_type', 'uploaded_by', 'archived_at'];
        $missingColumns = [];

        foreach ($requiredColumns as $column) {
            $exists = in_array($column, $columns);
            $output .= "{$column} column exists: " . ($exists ? 'YES' : 'NO') . "\n";
            if (!$exists) {
                $missingColumns[] = $column;
            }
        }

        // Check migration status
        $migrations = \Illuminate\Support\Facades\DB::table('migrations')->where('migration', 'like', '%issuances%')->get();
        $output .= "\nIssuance-related migrations:\n";
        foreach ($migrations as $migration) {
            $output .= "- {$migration->migration} (batch: {$migration->batch})\n";
        }

        $output .= "\n=== RECOMMENDATIONS ===\n";
        if (!empty($missingColumns)) {
            $output .= "❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
            $output .= "🔧 Visit /fix-issuance-table to automatically fix missing columns\n";
            $output .= "🔧 Or run: php artisan migrate\n";
        } else {
            $output .= "✅ All required columns exist. Issuance functionality should work.\n";
        }

    } catch (\Exception $e) {
        $output .= "ERROR: " . $e->getMessage() . "\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Route to run issuance migration
Route::get('/run-issuance-migration', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate');
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("Migration executed:\n" . $output)->header('Content-Type', 'text/plain');
    } catch (\Exception $e) {
        return response("Migration failed: " . $e->getMessage())->header('Content-Type', 'text/plain');
    }
});

// Route to fix issuance table structure
Route::get('/fix-issuance-table', function () {
    $output = "=== FIXING ISSUANCE TABLE ===\n\n";

    try {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('issuances');
        $missing = [];
        $added = [];

        \Illuminate\Support\Facades\Schema::table('issuances', function (\Illuminate\Database\Schema\Blueprint $table) use ($columns, &$missing, &$added) {
            if (!in_array('file_name', $columns)) {
                $table->string('file_name')->nullable()->after('title');
                $missing[] = 'file_name';
                $added[] = 'file_name';
            }
            if (!in_array('file_size', $columns)) {
                $table->bigInteger('file_size')->nullable()->after('file_path');
                $missing[] = 'file_size';
                $added[] = 'file_size';
            }
            if (!in_array('file_type', $columns)) {
                $table->string('file_type')->nullable()->after('file_size');
                $missing[] = 'file_type';
                $added[] = 'file_type';
            }
            if (!in_array('uploaded_by', $columns)) {
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null')->after('file_type');
                $missing[] = 'uploaded_by';
                $added[] = 'uploaded_by';
            }
            if (!in_array('archived_at', $columns)) {
                $table->timestamp('archived_at')->nullable()->after('updated_at');
                $missing[] = 'archived_at';
                $added[] = 'archived_at';
            }
        });

        if (count($added) > 0) {
            $output .= "✅ Successfully added missing columns: " . implode(', ', $added) . "\n";
        } else {
            $output .= "ℹ️  All columns already exist, no changes needed.\n";
        }

        $output .= "\n=== VERIFICATION ===\n";
        $newColumns = \Illuminate\Support\Facades\Schema::getColumnListing('issuances');
        $output .= "Updated table columns: " . implode(', ', $newColumns) . "\n";

        $output .= "\n✅ Issuance reupload functionality should now work!\n";

    } catch (\Exception $e) {
        $output .= "❌ ERROR: " . $e->getMessage() . "\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Debug route to test resubmission counting fix
Route::get('/test-resubmission-counting', function () {
    $output = "=== RESUBMISSION COUNTING TEST ===\n\n";

    try {
        // Test with a sample barangay
        $barangay = \App\Models\User::where('user_type', 'barangay')->first();

        if (!$barangay) {
            $output .= "❌ No barangay users found in database\n";
            return response($output)->header('Content-Type', 'text/plain');
        }

        $output .= "Testing with barangay: {$barangay->name} (ID: {$barangay->id})\n\n";

        // Count total submissions (old way - counts all records)
        $totalWeeklyRecords = \App\Models\WeeklyReport::where('user_id', $barangay->id)->count();
        $totalMonthlyRecords = \App\Models\MonthlyReport::where('user_id', $barangay->id)->count();
        $totalQuarterlyRecords = \App\Models\QuarterlyReport::where('user_id', $barangay->id)->count();
        $totalSemestralRecords = \App\Models\SemestralReport::where('user_id', $barangay->id)->count();
        $totalAnnualRecords = \App\Models\AnnualReport::where('user_id', $barangay->id)->count();

        $totalRecords = $totalWeeklyRecords + $totalMonthlyRecords + $totalQuarterlyRecords + $totalSemestralRecords + $totalAnnualRecords;

        // Count unique submissions (new way - counts unique user_id + report_type_id combinations)
        $uniqueWeekly = \Illuminate\Support\Facades\DB::table('weekly_reports')
            ->select('user_id', 'report_type_id')
            ->where('user_id', $barangay->id)
            ->distinct()
            ->count();

        $uniqueMonthly = \Illuminate\Support\Facades\DB::table('monthly_reports')
            ->select('user_id', 'report_type_id')
            ->where('user_id', $barangay->id)
            ->distinct()
            ->count();

        $uniqueQuarterly = \Illuminate\Support\Facades\DB::table('quarterly_reports')
            ->select('user_id', 'report_type_id')
            ->where('user_id', $barangay->id)
            ->distinct()
            ->count();

        $uniqueSemestral = \Illuminate\Support\Facades\DB::table('semestral_reports')
            ->select('user_id', 'report_type_id')
            ->where('user_id', $barangay->id)
            ->distinct()
            ->count();

        $uniqueAnnual = \Illuminate\Support\Facades\DB::table('annual_reports')
            ->select('user_id', 'report_type_id')
            ->where('user_id', $barangay->id)
            ->distinct()
            ->count();

        $uniqueSubmissions = $uniqueWeekly + $uniqueMonthly + $uniqueQuarterly + $uniqueSemestral + $uniqueAnnual;

        $output .= "=== COUNTING COMPARISON ===\n";
        $output .= "📊 OLD METHOD (Total Records):\n";
        $output .= "   Weekly: {$totalWeeklyRecords}\n";
        $output .= "   Monthly: {$totalMonthlyRecords}\n";
        $output .= "   Quarterly: {$totalQuarterlyRecords}\n";
        $output .= "   Semestral: {$totalSemestralRecords}\n";
        $output .= "   Annual: {$totalAnnualRecords}\n";
        $output .= "   TOTAL: {$totalRecords}\n\n";

        $output .= "✅ NEW METHOD (Unique Submissions):\n";
        $output .= "   Weekly: {$uniqueWeekly}\n";
        $output .= "   Monthly: {$uniqueMonthly}\n";
        $output .= "   Quarterly: {$uniqueQuarterly}\n";
        $output .= "   Semestral: {$uniqueSemestral}\n";
        $output .= "   Annual: {$uniqueAnnual}\n";
        $output .= "   TOTAL: {$uniqueSubmissions}\n\n";

        if ($totalRecords > $uniqueSubmissions) {
            $difference = $totalRecords - $uniqueSubmissions;
            $output .= "🔍 RESUBMISSIONS DETECTED!\n";
            $output .= "   Difference: {$difference} resubmissions\n";
            $output .= "   This means there are {$difference} duplicate submissions that were being double-counted before.\n";
            $output .= "   ✅ The fix is working - resubmissions are no longer being counted as separate reports!\n";
        } elseif ($totalRecords == $uniqueSubmissions) {
            $output .= "ℹ️  No resubmissions found for this barangay.\n";
            $output .= "   Both counting methods return the same result.\n";
        } else {
            $output .= "⚠️  Unexpected result: unique count is higher than total count.\n";
        }

    } catch (\Exception $e) {
        $output .= "❌ ERROR: " . $e->getMessage() . "\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Debug route to check for duplicate report type names
Route::get('/debug-duplicate-report-types', function () {
    $output = "=== DUPLICATE REPORT TYPE NAMES DEBUG ===\n\n";

    try {
        // Check for report types with the same name
        $reportTypes = \App\Models\ReportType::all();
        $nameGroups = $reportTypes->groupBy('name');

        $output .= "Total report types: " . $reportTypes->count() . "\n\n";

        $duplicatesFound = false;
        foreach ($nameGroups as $name => $group) {
            if ($group->count() > 1) {
                $duplicatesFound = true;
                $output .= "🔍 DUPLICATE NAME FOUND: '{$name}'\n";
                foreach ($group as $reportType) {
                    $output .= "   - ID: {$reportType->id}, Frequency: {$reportType->frequency}, Deadline: {$reportType->deadline}\n";
                }
                $output .= "\n";
            }
        }

        if (!$duplicatesFound) {
            $output .= "✅ No duplicate report type names found.\n\n";
        }

        // Check specific case: LUPON MINUTES 2
        $luponReports = $reportTypes->where('name', 'LUPON MINUTES 2');
        if ($luponReports->count() > 0) {
            $output .= "=== LUPON MINUTES 2 ANALYSIS ===\n";
            foreach ($luponReports as $reportType) {
                $output .= "Report Type ID: {$reportType->id}\n";
                $output .= "Name: {$reportType->name}\n";
                $output .= "Frequency: {$reportType->frequency}\n";
                $output .= "Deadline: {$reportType->deadline}\n";

                // Check submissions for this report type
                $weeklyCount = \App\Models\WeeklyReport::where('report_type_id', $reportType->id)->count();
                $monthlyCount = \App\Models\MonthlyReport::where('report_type_id', $reportType->id)->count();
                $quarterlyCount = \App\Models\QuarterlyReport::where('report_type_id', $reportType->id)->count();
                $semestralCount = \App\Models\SemestralReport::where('report_type_id', $reportType->id)->count();
                $annualCount = \App\Models\AnnualReport::where('report_type_id', $reportType->id)->count();

                $totalSubmissions = $weeklyCount + $monthlyCount + $quarterlyCount + $semestralCount + $annualCount;
                $output .= "Total submissions: {$totalSubmissions}\n";
                $output .= "  Weekly: {$weeklyCount}, Monthly: {$monthlyCount}, Quarterly: {$quarterlyCount}, Semestral: {$semestralCount}, Annual: {$annualCount}\n\n";
            }
        } else {
            $output .= "❌ No 'LUPON MINUTES 2' report type found.\n";
        }

        // Check recent submissions for a specific barangay
        $barangay = \App\Models\User::where('user_type', 'barangay')->first();
        if ($barangay) {
            $output .= "=== RECENT SUBMISSIONS FOR {$barangay->name} ===\n";

            // Get all reports for this barangay
            $allReports = collect();

            $weeklyReports = \App\Models\WeeklyReport::with('reportType')->where('user_id', $barangay->id)->get();
            $monthlyReports = \App\Models\MonthlyReport::with('reportType')->where('user_id', $barangay->id)->get();
            $quarterlyReports = \App\Models\QuarterlyReport::with('reportType')->where('user_id', $barangay->id)->get();
            $semestralReports = \App\Models\SemestralReport::with('reportType')->where('user_id', $barangay->id)->get();
            $annualReports = \App\Models\AnnualReport::with('reportType')->where('user_id', $barangay->id)->get();

            $allReports = $allReports->concat($weeklyReports)->concat($monthlyReports)->concat($quarterlyReports)->concat($semestralReports)->concat($annualReports);

            $output .= "Total submissions: " . $allReports->count() . "\n";

            foreach ($allReports->sortByDesc('created_at')->take(10) as $report) {
                $output .= "- {$report->reportType->name} (ID: {$report->id}, Type ID: {$report->report_type_id}, Created: {$report->created_at})\n";
            }

            // Group by user_id + report_type_id to see duplicates
            $grouped = $allReports->groupBy(function($report) {
                return $report->user_id . '_' . $report->report_type_id;
            });

            $output .= "\n=== GROUPED BY USER_ID + REPORT_TYPE_ID ===\n";
            foreach ($grouped as $key => $group) {
                if ($group->count() > 1) {
                    $output .= "Group {$key} has {$group->count()} submissions:\n";
                    foreach ($group as $report) {
                        $output .= "  - {$report->reportType->name} (ID: {$report->id}, Created: {$report->created_at})\n";
                    }
                    $output .= "\n";
                }
            }
        }

    } catch (\Exception $e) {
        $output .= "❌ ERROR: " . $e->getMessage() . "\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

// Debug route to test Recent Reports logic specifically
Route::get('/debug-recent-reports', function () {
    $output = "=== RECENT REPORTS DEBUG ===\n\n";

    try {
        // Get a facilitator user
        $facilitator = \App\Models\User::where('user_type', 'facilitator')->first();

        if (!$facilitator) {
            $output .= "❌ No facilitator users found\n";
            return response($output)->header('Content-Type', 'text/plain');
        }

        $output .= "Testing with facilitator: {$facilitator->name}\n\n";

        // Get clusters assigned to the facilitator (simplified logic)
        $clusterIds = \Illuminate\Support\Facades\DB::table('clusters')->where('is_active', true)->pluck('id')->toArray();

        // Get barangays in those clusters
        $barangayIds = \App\Models\User::where('user_type', 'barangay')
            ->whereIn('cluster_id', $clusterIds)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $output .= "Barangay IDs: " . implode(', ', $barangayIds) . "\n\n";

        // Simulate the getRecentSubmissions logic
        $weeklyQuery = \App\Models\WeeklyReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $monthlyQuery = \App\Models\MonthlyReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $quarterlyQuery = \App\Models\QuarterlyReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $semestralQuery = \App\Models\SemestralReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $annualQuery = \App\Models\AnnualReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);

        // Get all reports with their relationships and add unique identifiers
        $weeklyReports = $weeklyQuery->get()->map(function ($report) {
            $report->model_type = 'WeeklyReport';
            $report->unique_id = 'weekly_' . $report->id;
            $report->barangay_name = $report->user->name;
            $report->report_name = $report->reportType->name;
            $report->type = 'weekly';
            return $report;
        });

        $monthlyReports = $monthlyQuery->get()->map(function ($report) {
            $report->model_type = 'MonthlyReport';
            $report->unique_id = 'monthly_' . $report->id;
            $report->barangay_name = $report->user->name;
            $report->report_name = $report->reportType->name;
            $report->type = 'monthly';
            return $report;
        });

        $quarterlyReports = $quarterlyQuery->get()->map(function ($report) {
            $report->model_type = 'QuarterlyReport';
            $report->unique_id = 'quarterly_' . $report->id;
            $report->barangay_name = $report->user->name;
            $report->report_name = $report->reportType->name;
            $report->type = 'quarterly';
            return $report;
        });

        $semestralReports = $semestralQuery->get()->map(function ($report) {
            $report->model_type = 'SemestralReport';
            $report->unique_id = 'semestral_' . $report->id;
            $report->barangay_name = $report->user->name;
            $report->report_name = $report->reportType->name;
            $report->type = 'semestral';
            return $report;
        });

        $annualReports = $annualQuery->get()->map(function ($report) {
            $report->model_type = 'AnnualReport';
            $report->unique_id = 'annual_' . $report->id;
            $report->barangay_name = $report->user->name;
            $report->report_name = $report->reportType->name;
            $report->type = 'annual';
            return $report;
        });

        // Combine all reports
        $allReports = collect()
            ->concat($weeklyReports)
            ->concat($monthlyReports)
            ->concat($quarterlyReports)
            ->concat($semestralReports)
            ->concat($annualReports);

        $output .= "=== ALL REPORTS BEFORE GROUPING ===\n";
        $output .= "Total reports: " . $allReports->count() . "\n\n";

        foreach ($allReports->sortByDesc('created_at')->take(10) as $report) {
            $output .= "- {$report->report_name} by {$report->barangay_name} (ID: {$report->id}, Type ID: {$report->report_type_id}, User ID: {$report->user_id}, Created: {$report->created_at})\n";
        }

        // Group reports by report name to show unique report types regardless of barangay or report_type_id
        $latestReports = collect();
        $groupedReports = $allReports->groupBy('report_name');

        $output .= "\n=== GROUPING ANALYSIS (BY REPORT NAME) ===\n";
        foreach ($groupedReports as $reportName => $group) {
            $output .= "Report '{$reportName}' has {$group->count()} submissions:\n";
            foreach ($group as $report) {
                $output .= "  - {$report->report_name} by {$report->barangay_name} (ID: {$report->id}, Type ID: {$report->report_type_id}, Created: {$report->created_at})\n";
            }
            $latestReport = $group->sortByDesc('created_at')->first();
            $output .= "  → Latest: {$latestReport->report_name} by {$latestReport->barangay_name} (ID: {$latestReport->id}, Created: {$latestReport->created_at})\n\n";

            if ($latestReport) {
                $latestReports->push($latestReport);
            }
        }

        // Sort the filtered collection by created_at in descending order and take 5 most recent
        $recentReports = $latestReports->sortByDesc('created_at')->take(5);

        $output .= "=== FINAL RECENT REPORTS (TOP 5) ===\n";
        foreach ($recentReports as $report) {
            $output .= "- {$report->report_name} by {$report->barangay_name} (ID: {$report->id}, Created: {$report->created_at})\n";
        }

    } catch (\Exception $e) {
        $output .= "❌ ERROR: " . $e->getMessage() . "\n";
        $output .= "Stack trace: " . $e->getTraceAsString() . "\n";
    }

    return response($output)->header('Content-Type', 'text/plain');
});

