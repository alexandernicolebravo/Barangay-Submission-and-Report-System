<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cluster;
use Illuminate\Support\Facades\Hash;
use App\Models\ReportType;
use App\Models\WeeklyReport;
use App\Models\MonthlyReport;
use App\Models\QuarterlyReport;
use App\Models\SemestralReport;
use App\Models\AnnualReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with users list.
     */
    public function index()
    {
        // Get total submissions count
        $totalSubmissions = WeeklyReport::count() +
                          MonthlyReport::count() +
                          QuarterlyReport::count() +
                          AnnualReport::count();

        // Get submitted reports count
        $submittedReports = WeeklyReport::where('status', 'submitted')->count() +
                            MonthlyReport::where('status', 'submitted')->count() +
                            QuarterlyReport::where('status', 'submitted')->count() +
                            AnnualReport::where('status', 'submitted')->count();

        // Get no submission reports count
        $noSubmissionReports = WeeklyReport::where('status', 'no submission')->count() +
                               MonthlyReport::where('status', 'no submission')->count() +
                               QuarterlyReport::where('status', 'no submission')->count() +
                               AnnualReport::where('status', 'no submission')->count();

        // Get late submissions count
        $lateSubmissions = DB::table('weekly_reports')
            ->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id')
            ->where('weekly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('weekly_reports.status', 'submitted')
            ->count() +
            DB::table('monthly_reports')
            ->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id')
            ->where('monthly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('monthly_reports.status', 'submitted')
            ->count() +
            DB::table('quarterly_reports')
            ->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id')
            ->where('quarterly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('quarterly_reports.status', 'submitted')
            ->count() +
            DB::table('annual_reports')
            ->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id')
            ->where('annual_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('annual_reports.status', 'submitted')
            ->count();

        // Get recent submissions
        $recentSubmissions = DB::table('weekly_reports')
            ->select([
                'weekly_reports.id',
                'weekly_reports.user_id',
                'weekly_reports.report_type_id',
                'weekly_reports.status',
                'weekly_reports.created_at as submitted_at',
                'users.name as submitter',
                'report_types.name as report_type',
                DB::raw('CASE WHEN weekly_reports.status = "pending" AND weekly_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
            ])
            ->join('users', 'weekly_reports.user_id', '=', 'users.id')
            ->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id')
            ->union(
                DB::table('monthly_reports')
                ->select([
                    'monthly_reports.id',
                    'monthly_reports.user_id',
                    'monthly_reports.report_type_id',
                    'monthly_reports.status',
                    'monthly_reports.created_at as submitted_at',
                    'users.name as submitter',
                    'report_types.name as report_type',
                    DB::raw('CASE WHEN monthly_reports.status = "submitted" AND monthly_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
                ])
                ->join('users', 'monthly_reports.user_id', '=', 'users.id')
                ->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id')
            )
            ->union(
                DB::table('quarterly_reports')
                ->select([
                    'quarterly_reports.id',
                    'quarterly_reports.user_id',
                    'quarterly_reports.report_type_id',
                    'quarterly_reports.status',
                    'quarterly_reports.created_at as submitted_at',
                    'users.name as submitter',
                    'report_types.name as report_type',
                    DB::raw('CASE WHEN quarterly_reports.status = "submitted" AND quarterly_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
                ])
                ->join('users', 'quarterly_reports.user_id', '=', 'users.id')
                ->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id')
            )
            ->union(
                DB::table('annual_reports')
                ->select([
                    'annual_reports.id',
                    'annual_reports.user_id',
                    'annual_reports.report_type_id',
                    'annual_reports.status',
                    'annual_reports.created_at as submitted_at',
                    'users.name as submitter',
                    'report_types.name as report_type',
                    DB::raw('CASE WHEN annual_reports.status = "submitted" AND annual_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
                ])
                ->join('users', 'annual_reports.user_id', '=', 'users.id')
                ->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id')
            )
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        // Get submissions by type for chart
        $weeklyCount = WeeklyReport::count();
        $monthlyCount = MonthlyReport::count();
        $quarterlyCount = QuarterlyReport::count();
        $annualCount = AnnualReport::count();

        return view('admin.dashboard', compact(
            'totalSubmissions',
            'submittedReports',
            'noSubmissionReports',
            'lateSubmissions',
            'recentSubmissions',
            'weeklyCount',
            'monthlyCount',
            'quarterlyCount',
            'annualCount'
        ));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'user_type' => 'required|in:admin,facilitator,barangay',
            'cluster_id' => 'nullable|exists:clusters,id',
            'clusters' => 'nullable|array',
            'clusters.*' => 'exists:clusters,id',
        ]);

        if ($request->user_type === 'barangay') {
            $clusterExists = Cluster::where('is_active', true)->exists();
            if (!$clusterExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'At least one active cluster must exist before adding a barangay.'
                    ], 422);
                }
                return back()->with('error', 'At least one active cluster must exist before adding a barangay.');
            }

            if (!$request->cluster_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Barangays must be assigned to a cluster.'
                    ], 422);
                }
                return back()->with('error', 'Barangays must be assigned to a cluster.');
            }

            // Verify that the selected cluster is active
            $cluster = Cluster::find($request->cluster_id);
            if (!$cluster || !$cluster->is_active) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a valid active cluster.'
                    ], 422);
                }
                return back()->with('error', 'Please select a valid active cluster.');
            }
        }

        DB::beginTransaction();
        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->user_type, // For backward compatibility
                'user_type' => $request->user_type,
                'cluster_id' => $request->user_type === 'barangay' ? $request->cluster_id : null,
            ]);

            // If the user is a facilitator, assign them to the selected clusters
            if ($request->user_type === 'facilitator' && $request->has('clusters')) {
                foreach ($request->clusters as $clusterId) {
                    DB::table('facilitator_cluster')->insert([
                        'user_id' => $user->id,
                        'cluster_id' => $clusterId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            // If this is an AJAX request, return a JSON response
            if ($request->ajax()) {
                // Get the created user with relationships
                $createdUser = User::with(['cluster', 'assignedClusters'])->find($user->id);

                // Add assigned_clusters property for easier access
                $createdUser->assigned_clusters = $createdUser->assignedClusters->pluck('id')->toArray();

                // Add cluster names for facilitators
                if ($createdUser->user_type === 'facilitator' || $createdUser->role === 'facilitator') {
                    $createdUser->assigned_clusters_names = $createdUser->assignedClusters->pluck('name')->implode(', ');
                }

                return response()->json([
                    'success' => true,
                    'message' => ucfirst($request->user_type) . ' account created successfully.',
                    'user' => $createdUser
                ]);
            }

            return back()->with('success', ucfirst($request->user_type) . ' account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create user: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Confirm deactivation of a user.
     */
    public function confirmDeactivation($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'cluster') {
            $barangayExists = User::where('role', 'barangay')
                ->where('cluster_id', $user->id)
                ->exists();

            if ($barangayExists) {
                return response()->json([
                    'confirm' => $user->is_active
                        ? 'This cluster has assigned barangays. Are you sure you want to deactivate it?'
                        : 'This cluster has assigned barangays. Are you sure you want to reactivate it?'
                ]);
            }
        }

        return response()->json([
            'confirm' => $user->is_active
                ? 'Are you sure you want to deactivate this barangay?'
                : 'Are you sure you want to reactivate this barangay?'
        ]);
    }

    /**
     * Deactivate or activate a user.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($user->role) . ' status updated to ' . ($user->is_active ? 'active' : 'inactive') . '.',
                'user' => $user
            ]);
        }

        return back()->with('success', ucfirst($user->role) . ' status updated to ' . ($user->is_active ? 'active' : 'inactive') . '.');
    }

    /**
     * Display the user management page.
     */
    public function userManagement()
    {
        // Get all users with their relationships for client-side filtering
        // Sort users by role (admin, facilitator, barangay) and then by cluster_id for barangays
        $users = User::with(['cluster', 'assignedClusters'])
            ->orderByRaw("
                CASE
                    WHEN role = 'admin' OR user_type = 'admin' THEN 1
                    WHEN role = 'facilitator' OR user_type = 'facilitator' THEN 2
                    WHEN role = 'barangay' OR user_type = 'barangay' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('cluster_id') // Sort barangays by cluster_id
            ->orderBy('name') // Then sort by name within each group
            ->get();

        // Add assigned_clusters property to each user for easier access in the view
        $users->each(function ($user) {
            $user->assigned_clusters = $user->assignedClusters->pluck('id')->toArray();

            // Add cluster names for facilitators
            if ($user->user_type === 'facilitator' || $user->role === 'facilitator') {
                $user->assigned_clusters_names = $user->assignedClusters->pluck('name')->implode(', ');
            }
        });

        return view('admin.user-management', compact('users'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'user_type' => 'required|in:admin,facilitator,barangay',
            'cluster_id' => 'nullable|exists:clusters,id',
            'clusters' => 'nullable|array',
            'clusters.*' => 'exists:clusters,id',
        ]);

        if ($request->user_type === 'barangay') {
            if (!$request->cluster_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Barangays must be assigned to a cluster.'
                    ], 422);
                }
                return back()->with('error', 'Barangays must be assigned to a cluster.');
            }

            // Verify that the selected cluster is active
            $cluster = Cluster::find($request->cluster_id);
            if (!$cluster || !$cluster->is_active) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a valid active cluster.'
                    ], 422);
                }
                return back()->with('error', 'Please select a valid active cluster.');
            }
        }

        DB::beginTransaction();
        try {
            // Update the user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->user_type, // For backward compatibility
                'user_type' => $request->user_type,
                'cluster_id' => $request->user_type === 'barangay' ? $request->cluster_id : null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|min:6',
                ]);
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // If the user is a facilitator, update their cluster assignments
            if ($request->user_type === 'facilitator') {
                // Remove existing assignments
                DB::table('facilitator_cluster')->where('user_id', $user->id)->delete();

                // Add new assignments
                if ($request->has('clusters')) {
                    foreach ($request->clusters as $clusterId) {
                        DB::table('facilitator_cluster')->insert([
                            'user_id' => $user->id,
                            'cluster_id' => $clusterId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            // If this is an AJAX request, return a JSON response
            if ($request->ajax()) {
                // Get the updated user with relationships
                $updatedUser = User::with(['cluster', 'assignedClusters'])->find($user->id);

                // Add assigned_clusters property for easier access
                $updatedUser->assigned_clusters = $updatedUser->assignedClusters->pluck('id')->toArray();

                // Add cluster names for facilitators
                if ($updatedUser->user_type === 'facilitator' || $updatedUser->role === 'facilitator') {
                    $updatedUser->assigned_clusters_names = $updatedUser->assignedClusters->pluck('name')->implode(', ');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'user' => $updatedUser
                ]);
            }

            return back()->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function viewSubmissions(Request $request)
    {
        try {
            // Get all barangays for the filter dropdown
            $barangays = User::where('role', 'barangay')->orWhere('user_type', 'barangay')->get();
            $perPage = $request->get('per_page', 10);
            $selectedBarangay = null;

            // Initialize queries with relationships
            $weeklyQuery = WeeklyReport::with(['user', 'reportType']);
            $monthlyQuery = MonthlyReport::with(['user', 'reportType']);
            $quarterlyQuery = QuarterlyReport::with(['user', 'reportType']);
            $semestralQuery = SemestralReport::with(['user', 'reportType']);
            $annualQuery = AnnualReport::with(['user', 'reportType']);

            // Filter by barangay (user) if specified
            if ($request->filled('barangay_id')) {
                $barangayId = $request->barangay_id;
                $weeklyQuery->where('user_id', $barangayId);
                $monthlyQuery->where('user_id', $barangayId);
                $quarterlyQuery->where('user_id', $barangayId);
                $semestralQuery->where('user_id', $barangayId);
                $annualQuery->where('user_id', $barangayId);

                // Get the selected barangay for the view
                $selectedBarangay = User::find($barangayId);
            }

            // Handle cluster filter
            if ($request->has('cluster_id') && !empty($request->cluster_id)) {
                $clusterId = $request->cluster_id;
                $weeklyQuery->whereHas('user', function($q) use ($clusterId) {
                    $q->where('cluster_id', $clusterId);
                });
                $monthlyQuery->whereHas('user', function($q) use ($clusterId) {
                    $q->where('cluster_id', $clusterId);
                });
                $quarterlyQuery->whereHas('user', function($q) use ($clusterId) {
                    $q->where('cluster_id', $clusterId);
                });
                $semestralQuery->whereHas('user', function($q) use ($clusterId) {
                    $q->where('cluster_id', $clusterId);
                });
                $annualQuery->whereHas('user', function($q) use ($clusterId) {
                    $q->where('cluster_id', $clusterId);
                });
            }

            // Apply type filter if specified
            if ($request->filled('type')) {
                $type = $request->type;
                // Only get reports of the specified type
                if ($type == 'weekly') {
                    $monthlyQuery->whereRaw('1=0'); // Force empty result
                    $quarterlyQuery->whereRaw('1=0');
                    $semestralQuery->whereRaw('1=0');
                    $annualQuery->whereRaw('1=0');
                } elseif ($type == 'monthly') {
                    $weeklyQuery->whereRaw('1=0');
                    $quarterlyQuery->whereRaw('1=0');
                    $semestralQuery->whereRaw('1=0');
                    $annualQuery->whereRaw('1=0');
                } elseif ($type == 'quarterly') {
                    $weeklyQuery->whereRaw('1=0');
                    $monthlyQuery->whereRaw('1=0');
                    $semestralQuery->whereRaw('1=0');
                    $annualQuery->whereRaw('1=0');
                } elseif ($type == 'semestral') {
                    $weeklyQuery->whereRaw('1=0');
                    $monthlyQuery->whereRaw('1=0');
                    $quarterlyQuery->whereRaw('1=0');
                    $annualQuery->whereRaw('1=0');
                } elseif ($type == 'annual') {
                    $weeklyQuery->whereRaw('1=0');
                    $monthlyQuery->whereRaw('1=0');
                    $quarterlyQuery->whereRaw('1=0');
                    $semestralQuery->whereRaw('1=0');
                }
            }

            // Apply search filter if specified
            if ($request->filled('search')) {
                $search = $request->search;
                $weeklyQuery->whereHas('reportType', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });

                $monthlyQuery->whereHas('reportType', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });

                $quarterlyQuery->whereHas('reportType', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });

                $semestralQuery->whereHas('reportType', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });

                $annualQuery->whereHas('reportType', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // Get all reports with their relationships and add unique identifiers
            $weeklyReports = $weeklyQuery->get()->map(function ($report) {
                $report->model_type = 'WeeklyReport';
                $report->unique_id = 'weekly_' . $report->id;
                return $report;
            });

            $monthlyReports = $monthlyQuery->get()->map(function ($report) {
                $report->model_type = 'MonthlyReport';
                $report->unique_id = 'monthly_' . $report->id;
                return $report;
            });

            $quarterlyReports = $quarterlyQuery->get()->map(function ($report) {
                $report->model_type = 'QuarterlyReport';
                $report->unique_id = 'quarterly_' . $report->id;
                return $report;
            });

            $semestralReports = $semestralQuery->get()->map(function ($report) {
                $report->model_type = 'SemestralReport';
                $report->unique_id = 'semestral_' . $report->id;
                return $report;
            });

            $annualReports = $annualQuery->get()->map(function ($report) {
                $report->model_type = 'AnnualReport';
                $report->unique_id = 'annual_' . $report->id;
                return $report;
            });

            // Combine all reports
            $allReports = collect()
                ->concat($weeklyReports)
                ->concat($monthlyReports)
                ->concat($quarterlyReports)
                ->concat($semestralReports)
                ->concat($annualReports);

            // Group reports by user_id and report_type_id and get only the latest submission for each combination
            $latestReports = collect();
            $groupedReports = $allReports->groupBy(function($report) {
                return $report->user_id . '_' . $report->report_type_id;
            });

            foreach ($groupedReports as $group) {
                // Sort by created_at in descending order and take the first one (latest)
                $latestReport = $group->sortByDesc('created_at')->first();
                if ($latestReport) {
                    $latestReports->push($latestReport);
                }
            }

            // Sort the filtered collection by created_at in descending order
            $reports = $latestReports->sortByDesc('created_at');

            // Apply timeliness filter if specified
            if ($request->filled('timeliness')) {
                $timeliness = $request->timeliness;
                $reports = $reports->filter(function($report) use ($timeliness) {
                    $isLate = \Carbon\Carbon::parse($report->created_at)->isAfter($report->reportType->deadline);
                    return ($timeliness === 'late') ? $isLate : !$isLate;
                });
            }

            // Create a paginator
            $page = $request->get('page', 1);

            // Only create a paginator if there are reports
            if ($reports->count() > 0) {
                $reports = new \Illuminate\Pagination\LengthAwarePaginator(
                    $reports->forPage($page, $perPage),
                    $reports->count(),
                    $perPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query()
                    ]
                );
            } else {
                // Create an empty paginator
                $reports = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect(),
                    0,
                    $perPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query()
                    ]
                );
            }

            // Check if this is an AJAX request
            if ($request->ajax()) {
                return view('admin.partials.submissions-table', compact('reports', 'selectedBarangay'))->render();
            }

            // Return the full view for non-AJAX requests
            return view('admin.view-submissions', compact('reports', 'barangays', 'selectedBarangay'));
        } catch (\Exception $e) {
            Log::error('Error in admin view submissions: ' . $e->getMessage());
            return view('admin.view-submissions', [
                'reports' => collect(),
                'barangays' => User::where('role', 'barangay')->where('is_active', true)->get(),
                'selectedBarangay' => null
            ])->with('error', 'An error occurred while loading submissions: ' . $e->getMessage());
        }
    }
}
