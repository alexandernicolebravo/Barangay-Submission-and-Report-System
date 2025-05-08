<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\ReportType;
use App\Models\WeeklyReport;
use App\Models\MonthlyReport;
use App\Models\QuarterlyReport;
use App\Models\AnnualReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Get approved submissions count
        $approvedSubmissions = WeeklyReport::where('status', 'approved')->count() +
                             MonthlyReport::where('status', 'approved')->count() +
                             QuarterlyReport::where('status', 'approved')->count() +
                             AnnualReport::where('status', 'approved')->count();

        // Get pending submissions count
        $pendingSubmissions = WeeklyReport::where('status', 'pending')->count() +
                            MonthlyReport::where('status', 'pending')->count() +
                            QuarterlyReport::where('status', 'pending')->count() +
                            AnnualReport::where('status', 'pending')->count();

        // Get late submissions count
        $lateSubmissions = DB::table('weekly_reports')
            ->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id')
            ->where('weekly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->count() +
            DB::table('monthly_reports')
            ->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id')
            ->where('monthly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->count() +
            DB::table('quarterly_reports')
            ->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id')
            ->where('quarterly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->count() +
            DB::table('annual_reports')
            ->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id')
            ->where('annual_reports.created_at', '>', DB::raw('report_types.deadline'))
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
                DB::raw('CASE WHEN weekly_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
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
                    DB::raw('CASE WHEN monthly_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
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
                    DB::raw('CASE WHEN quarterly_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
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
                    DB::raw('CASE WHEN annual_reports.created_at > report_types.deadline THEN true ELSE false END as is_late')
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
            'approvedSubmissions',
            'pendingSubmissions',
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
            'role' => 'required|in:cluster,barangay',
            'cluster_id' => 'nullable|exists:users,id',
        ]);

        if ($request->role === 'barangay') {
            $clusterExists = User::where('role', 'cluster')->exists();
            if (!$clusterExists) {
                return back()->with('error', 'A cluster must be created before adding a barangay.');
            }

            if (!$request->cluster_id) {
                return back()->with('error', 'Barangays must be assigned to a cluster.');
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'cluster_id' => $request->cluster_id,
        ]);

        return back()->with('success', ucfirst($request->role) . ' account created successfully.');
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
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', ucfirst($user->role) . ' status updated to ' . ($user->is_active ? 'active' : 'inactive') . '.');
    }

    public function userManagement()
    {
        $users = User::where('role', '!=', 'admin')->get();
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
            'role' => 'required|in:cluster,barangay',
            'cluster_id' => 'nullable|exists:users,id',
        ]);

        if ($request->role === 'barangay') {
            if (!$request->cluster_id) {
                return back()->with('error', 'Barangays must be assigned to a cluster.');
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'cluster_id' => $request->cluster_id,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:6',
            ]);
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return back()->with('success', 'User updated successfully.');
    }
}
