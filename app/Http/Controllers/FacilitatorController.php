<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Models\MonthlyReport;
use App\Models\QuarterlyReport;
use App\Models\SemestralReport;
use App\Models\AnnualReport;
use App\Models\ExecutiveOrder;
use App\Models\ReportType;
use App\Models\Cluster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Notifications\ReportRemarksNotification;

class FacilitatorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // The middleware is already applied in the routes file
        // This is just for additional security
    }

    /**
     * Show the facilitator dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard(Request $request)
    {
        /** @var \App\Models\User $facilitator */
        $facilitator = Auth::user();

        // Get filter parameters
        $reportType = $request->input('report_type');
        $clusterId = $request->input('cluster_id');
        $status = $request->input('status');
        $timeliness = $request->input('timeliness');

        // Get clusters assigned to the facilitator
        // Check if the assignedClusters relationship exists and is callable
        if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
            $clusterIds = $facilitator->assignedClusters()->pluck('clusters.id')->toArray();
        } else {
            // Fallback: get all clusters if the relationship doesn't exist
            $clusterIds = DB::table('clusters')->where('is_active', true)->pluck('id')->toArray();
        }

        // Apply cluster filter if specified
        $filteredClusterIds = $clusterIds;
        if ($clusterId && in_array($clusterId, $clusterIds)) {
            $filteredClusterIds = [$clusterId];
        }

        // Get barangays in those clusters
        $barangays = User::where('user_type', 'barangay')
            ->whereIn('cluster_id', $filteredClusterIds)
            ->where('is_active', true)
            ->get();

        $barangayIds = $barangays->pluck('id')->toArray();

        // Calculate statistics like admin dashboard
        // Count total ACTIVE report types with future deadlines (like barangay dashboard)
        $totalReportTypes = ReportType::active()->where('deadline', '>=', now())->count();

        // Get unique submitted reports (count each report type per barangay only once)
        // Use DISTINCT to prevent counting resubmissions as separate reports
        $weeklyQuery = DB::table('weekly_reports')
            ->select('weekly_reports.user_id', 'weekly_reports.report_type_id')
            ->distinct();

        $monthlyQuery = DB::table('monthly_reports')
            ->select('monthly_reports.user_id', 'monthly_reports.report_type_id')
            ->distinct();

        $quarterlyQuery = DB::table('quarterly_reports')
            ->select('quarterly_reports.user_id', 'quarterly_reports.report_type_id')
            ->distinct();

        $semestralQuery = DB::table('semestral_reports')
            ->select('semestral_reports.user_id', 'semestral_reports.report_type_id')
            ->distinct();

        $annualQuery = DB::table('annual_reports')
            ->select('annual_reports.user_id', 'annual_reports.report_type_id')
            ->distinct();

        $executiveOrderQuery = DB::table('executive_orders')
            ->select('executive_orders.user_id', 'executive_orders.report_type_id')
            ->distinct();

        // Add joins only when we need to check timeliness
        if ($timeliness === 'late') {
            $weeklyQuery->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id');
            $monthlyQuery->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id');
            $quarterlyQuery->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id');
            $semestralQuery->join('report_types', 'semestral_reports.report_type_id', '=', 'report_types.id');
            $annualQuery->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id');
            $executiveOrderQuery->join('report_types', 'executive_orders.report_type_id', '=', 'report_types.id');
        }

        // Apply status filter
        if ($status === 'submitted') {
            $weeklyQuery->where('weekly_reports.status', 'submitted');
            $monthlyQuery->where('monthly_reports.status', 'submitted');
            $quarterlyQuery->where('quarterly_reports.status', 'submitted');
            $semestralQuery->where('semestral_reports.status', 'submitted');
            $annualQuery->where('annual_reports.status', 'submitted');
            $executiveOrderQuery->where('executive_orders.status', 'submitted');
        } elseif ($status === 'no_submission') {
            // For no submission, we'll handle this differently in the calculation
            $weeklyQuery->whereRaw('1=0'); // Force empty results
            $monthlyQuery->whereRaw('1=0');
            $quarterlyQuery->whereRaw('1=0');
            $semestralQuery->whereRaw('1=0');
            $annualQuery->whereRaw('1=0');
            $executiveOrderQuery->whereRaw('1=0');
        } else {
            // Default: only show submitted reports
            $weeklyQuery->where('weekly_reports.status', 'submitted');
            $monthlyQuery->where('monthly_reports.status', 'submitted');
            $quarterlyQuery->where('quarterly_reports.status', 'submitted');
            $semestralQuery->where('semestral_reports.status', 'submitted');
            $annualQuery->where('annual_reports.status', 'submitted');
            $executiveOrderQuery->where('executive_orders.status', 'submitted');
        }

        // Apply timeliness filter
        if ($timeliness === 'late') {
            $weeklyQuery->whereRaw('weekly_reports.created_at > report_types.deadline');
            $monthlyQuery->whereRaw('monthly_reports.created_at > report_types.deadline');
            $quarterlyQuery->whereRaw('quarterly_reports.created_at > report_types.deadline');
            $semestralQuery->whereRaw('semestral_reports.created_at > report_types.deadline');
            $annualQuery->whereRaw('annual_reports.created_at > report_types.deadline');
            $executiveOrderQuery->whereRaw('executive_orders.created_at > report_types.deadline');
        }

        // Apply barangay filter (only facilitator's assigned clusters)
        if (!empty($barangayIds)) {
            $weeklyQuery->whereIn('weekly_reports.user_id', $barangayIds);
            $monthlyQuery->whereIn('monthly_reports.user_id', $barangayIds);
            $quarterlyQuery->whereIn('quarterly_reports.user_id', $barangayIds);
            $semestralQuery->whereIn('semestral_reports.user_id', $barangayIds);
            $annualQuery->whereIn('annual_reports.user_id', $barangayIds);
            $executiveOrderQuery->whereIn('executive_orders.user_id', $barangayIds);
        } else {
            // If no barangays, force empty results
            $weeklyQuery->whereRaw('1=0');
            $monthlyQuery->whereRaw('1=0');
            $quarterlyQuery->whereRaw('1=0');
            $semestralQuery->whereRaw('1=0');
            $annualQuery->whereRaw('1=0');
            $executiveOrderQuery->whereRaw('1=0');
        }



        // Apply report type filter if specified
        if ($reportType) {
            // Only count the specified report type
            if ($reportType != 'weekly') $weeklyQuery->whereRaw('1=0');
            if ($reportType != 'monthly') $monthlyQuery->whereRaw('1=0');
            if ($reportType != 'quarterly') $quarterlyQuery->whereRaw('1=0');
            if ($reportType != 'semestral') $semestralQuery->whereRaw('1=0');
            if ($reportType != 'annual') $annualQuery->whereRaw('1=0');
            if ($reportType != 'executive_order') $executiveOrderQuery->whereRaw('1=0');
        }

        // Execute the queries
        $weeklySubmitted = $weeklyQuery->groupBy('weekly_reports.user_id', 'weekly_reports.report_type_id')->get()->count();
        $monthlySubmitted = $monthlyQuery->groupBy('monthly_reports.user_id', 'monthly_reports.report_type_id')->get()->count();
        $quarterlySubmitted = $quarterlyQuery->groupBy('quarterly_reports.user_id', 'quarterly_reports.report_type_id')->get()->count();
        $semestralSubmitted = $semestralQuery->groupBy('semestral_reports.user_id', 'semestral_reports.report_type_id')->get()->count();
        $annualSubmitted = $annualQuery->groupBy('annual_reports.user_id', 'annual_reports.report_type_id')->get()->count();
        $executiveOrderSubmitted = $executiveOrderQuery->groupBy('executive_orders.user_id', 'executive_orders.report_type_id')->get()->count();

        // Total submitted reports (counting each report type per barangay only once)
        $totalSubmittedReports = $weeklySubmitted + $monthlySubmitted + $quarterlySubmitted + $semestralSubmitted + $annualSubmitted + $executiveOrderSubmitted;

        // Count total barangays in facilitator's assigned clusters
        $totalBarangays = count($barangayIds);

        // Calculate the total expected reports (report types × barangays)
        $totalExpectedReports = $totalReportTypes * $totalBarangays;

        // Calculate no submissions (expected - submitted)
        $noSubmissionReports = max(0, $totalExpectedReports - $totalSubmittedReports);

        // Get late submissions count
        $weeklyLateQuery = DB::table('weekly_reports')
            ->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id')
            ->where('weekly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('weekly_reports.status', 'submitted');

        $monthlyLateQuery = DB::table('monthly_reports')
                ->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id')
            ->where('monthly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('monthly_reports.status', 'submitted');

        $quarterlyLateQuery = DB::table('quarterly_reports')
                ->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id')
            ->where('quarterly_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('quarterly_reports.status', 'submitted');

        $semestralLateQuery = DB::table('semestral_reports')
            ->join('report_types', 'semestral_reports.report_type_id', '=', 'report_types.id')
            ->where('semestral_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('semestral_reports.status', 'submitted');

        $annualLateQuery = DB::table('annual_reports')
                ->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id')
            ->where('annual_reports.created_at', '>', DB::raw('report_types.deadline'))
            ->where('annual_reports.status', 'submitted');

        // Apply barangay filter (only facilitator's assigned clusters)
        if (!empty($barangayIds)) {
            $weeklyLateQuery->whereIn('weekly_reports.user_id', $barangayIds);
            $monthlyLateQuery->whereIn('monthly_reports.user_id', $barangayIds);
            $quarterlyLateQuery->whereIn('quarterly_reports.user_id', $barangayIds);
            $semestralLateQuery->whereIn('semestral_reports.user_id', $barangayIds);
            $annualLateQuery->whereIn('annual_reports.user_id', $barangayIds);
        } else {
            // If no barangays, force empty results
            $weeklyLateQuery->whereRaw('1=0');
            $monthlyLateQuery->whereRaw('1=0');
            $quarterlyLateQuery->whereRaw('1=0');
            $semestralLateQuery->whereRaw('1=0');
            $annualLateQuery->whereRaw('1=0');
        }



        // Apply report type filter if specified
        if ($reportType) {
            // Only count the specified report type
            if ($reportType != 'weekly') $weeklyLateQuery->whereRaw('1=0');
            if ($reportType != 'monthly') $monthlyLateQuery->whereRaw('1=0');
            if ($reportType != 'quarterly') $quarterlyLateQuery->whereRaw('1=0');
            if ($reportType != 'semestral') $semestralLateQuery->whereRaw('1=0');
            if ($reportType != 'annual') $annualLateQuery->whereRaw('1=0');
        }

        // Execute the queries
        $lateSubmissions = $weeklyLateQuery->count() +
                          $monthlyLateQuery->count() +
                          $quarterlyLateQuery->count() +
                          $semestralLateQuery->count() +
                          $annualLateQuery->count();

        // Get recent report submissions using the same logic as view-submissions
        $recentReports = $this->getRecentSubmissions($barangayIds);

        // Get upcoming deadlines
        $reportTypes = ReportType::all();
        $upcomingDeadlines = [];

        foreach ($reportTypes as $reportType) {
            $deadline = $this->calculateNextDeadline($reportType);
            if ($deadline) {
                $upcomingDeadlines[] = [
                    'report_type' => $reportType->name,
                    'frequency' => $reportType->frequency,
                    'deadline' => $deadline,
                    'days_remaining' => Carbon::now()->diffInDays($deadline, false)
                ];
            }
        }

        // Sort by days remaining (ascending)
        usort($upcomingDeadlines, function($a, $b) {
            return $a['days_remaining'] <=> $b['days_remaining'];
        });

        // Take only upcoming deadlines (positive days remaining)
        $upcomingDeadlines = array_filter($upcomingDeadlines, function($deadline) {
            return $deadline['days_remaining'] >= 0;
        });

        // Take only the next 5 deadlines
        $upcomingDeadlines = array_slice($upcomingDeadlines, 0, 5);

        // Get cluster submissions data
        $clusterSubmissions = $this->getClusterSubmissions($clusterIds, $reportType, $status, $timeliness);

        // Calculate on-time submissions
        $onTimeSubmissions = max(0, $totalSubmittedReports - $lateSubmissions);

        // Get assigned clusters for the facilitator
        $assignedClusters = [];
        if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
            $assignedClusters = $facilitator->assignedClusters()->get();
        }

        // Get barangay summary data
        $barangaySummary = $this->getBarangaySummary($barangays);

        // Get categorized barangays for submission lists
        $submissionCategories = $this->getSubmissionCategories($barangays);

        // Extract the arrays from submission categories
        $onTimeBarangays = $submissionCategories['onTimeBarangays'];
        $lateBarangays = $submissionCategories['lateBarangays'];
        $noSubmissionBarangays = $submissionCategories['noSubmissionBarangays'];

        return view('facilitator.dashboard', compact(
            'barangays',
            'recentReports',
            'upcomingDeadlines',
            'totalReportTypes',
            'totalSubmittedReports',
            'noSubmissionReports',
            'lateSubmissions',
            'onTimeSubmissions',
            'clusterSubmissions',
            'assignedClusters',
            'barangaySummary',
            'onTimeBarangays',
            'lateBarangays',
            'noSubmissionBarangays'
        ));
    }

    /**
     * Get dashboard chart data for AJAX requests
     */
    public function getDashboardChartData(Request $request)
    {
        /** @var \App\Models\User $facilitator */
        $facilitator = Auth::user();

        // Get filter parameters
        $reportType = $request->input('report_type');
        $clusterId = $request->input('cluster_id');
        $status = $request->input('status');
        $timeliness = $request->input('timeliness');

        // Get clusters assigned to the facilitator
        if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
            $clusterIds = $facilitator->assignedClusters()->pluck('clusters.id')->toArray();
        } else {
            $clusterIds = DB::table('clusters')->where('is_active', true)->pluck('id')->toArray();
        }

        // Apply cluster filter if specified
        $filteredClusterIds = $clusterIds;
        if ($clusterId && in_array($clusterId, $clusterIds)) {
            $filteredClusterIds = [$clusterId];
        }

        // Get barangays in those clusters
        $barangays = User::where('user_type', 'barangay')
            ->whereIn('cluster_id', $filteredClusterIds)
            ->where('is_active', true)
            ->get();

        $barangayIds = $barangays->pluck('id')->toArray();

        // Get chart data based on filters
        $chartData = $this->getFilteredChartData($barangayIds, $reportType, $status, $timeliness);

        // Get cluster submissions data
        $clusterSubmissions = $this->getClusterSubmissions($clusterIds, $reportType, $status, $timeliness);
        $chartData['clusterSubmissions'] = $clusterSubmissions;

        return response()->json($chartData);
    }

    /**
     * Get filtered chart data based on parameters
     */
    private function getFilteredChartData($barangayIds, $reportType = null, $status = null, $timeliness = null)
    {
        // Monthly trend data
        $currentYear = Carbon::now()->year;
        $submissionsByMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStartDate = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $monthEndDate = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();

            $monthlyTotal = 0;

            if (!empty($barangayIds)) {
                // Create queries for each report type
                $weeklyMonthQuery = WeeklyReport::whereBetween('created_at', [$monthStartDate, $monthEndDate])
                    ->whereIn('user_id', $barangayIds);
                $monthlyMonthQuery = MonthlyReport::whereBetween('created_at', [$monthStartDate, $monthEndDate])
                    ->whereIn('user_id', $barangayIds);
                $quarterlyMonthQuery = QuarterlyReport::whereBetween('created_at', [$monthStartDate, $monthEndDate])
                    ->whereIn('user_id', $barangayIds);
                $semestralMonthQuery = SemestralReport::whereBetween('created_at', [$monthStartDate, $monthEndDate])
                    ->whereIn('user_id', $barangayIds);
                $annualMonthQuery = AnnualReport::whereBetween('created_at', [$monthStartDate, $monthEndDate])
                    ->whereIn('user_id', $barangayIds);

                // Apply status filter
                if ($status === 'submitted') {
                    $weeklyMonthQuery->where('status', 'submitted');
                    $monthlyMonthQuery->where('status', 'submitted');
                    $quarterlyMonthQuery->where('status', 'submitted');
                    $semestralMonthQuery->where('status', 'submitted');
                    $annualMonthQuery->where('status', 'submitted');
                } elseif ($status !== 'no_submission') {
                    // Default: only show submitted reports
                    $weeklyMonthQuery->where('status', 'submitted');
                    $monthlyMonthQuery->where('status', 'submitted');
                    $quarterlyMonthQuery->where('status', 'submitted');
                    $semestralMonthQuery->where('status', 'submitted');
                    $annualMonthQuery->where('status', 'submitted');
                }

                // Apply timeliness filter
                if ($timeliness === 'late') {
                    $weeklyMonthQuery->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('weekly_reports.created_at > report_types.deadline');
                    $monthlyMonthQuery->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('monthly_reports.created_at > report_types.deadline');
                    $quarterlyMonthQuery->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('quarterly_reports.created_at > report_types.deadline');
                    $semestralMonthQuery->join('report_types', 'semestral_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('semestral_reports.created_at > report_types.deadline');
                    $annualMonthQuery->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('annual_reports.created_at > report_types.deadline');
                }

                // Apply report type filter
                if ($reportType) {
                    if ($reportType != 'weekly') $weeklyMonthQuery->whereRaw('1=0');
                    if ($reportType != 'monthly') $monthlyMonthQuery->whereRaw('1=0');
                    if ($reportType != 'quarterly') $quarterlyMonthQuery->whereRaw('1=0');
                    if ($reportType != 'semestral') $semestralMonthQuery->whereRaw('1=0');
                    if ($reportType != 'annual') $annualMonthQuery->whereRaw('1=0');
                }

                $monthlyTotal = $weeklyMonthQuery->count() +
                               $monthlyMonthQuery->count() +
                               $quarterlyMonthQuery->count() +
                               $semestralMonthQuery->count() +
                               $annualMonthQuery->count();
            }

            $submissionsByMonth[] = $monthlyTotal;
        }

        // Report type distribution data with DISTINCT to prevent counting resubmissions
        $reportTypeData = [];
        if (!empty($barangayIds)) {
            $weeklyCount = WeeklyReport::whereIn('user_id', $barangayIds)
                ->distinct('user_id', 'report_type_id');
            $monthlyCount = MonthlyReport::whereIn('user_id', $barangayIds)
                ->distinct('user_id', 'report_type_id');
            $quarterlyCount = QuarterlyReport::whereIn('user_id', $barangayIds)
                ->distinct('user_id', 'report_type_id');
            $semestralCount = SemestralReport::whereIn('user_id', $barangayIds)
                ->distinct('user_id', 'report_type_id');
            $annualCount = AnnualReport::whereIn('user_id', $barangayIds)
                ->distinct('user_id', 'report_type_id');
            $executiveOrderCount = ExecutiveOrder::whereIn('user_id', $barangayIds)
                ->distinct('user_id', 'report_type_id');

            // Apply status filter
            if ($status === 'submitted') {
                $weeklyCount->where('status', 'submitted');
                $monthlyCount->where('status', 'submitted');
                $quarterlyCount->where('status', 'submitted');
                $semestralCount->where('status', 'submitted');
                $annualCount->where('status', 'submitted');
                $executiveOrderCount->where('status', 'submitted');
            } elseif ($status !== 'no_submission') {
                $weeklyCount->where('status', 'submitted');
                $monthlyCount->where('status', 'submitted');
                $quarterlyCount->where('status', 'submitted');
                $semestralCount->where('status', 'submitted');
                $annualCount->where('status', 'submitted');
                $executiveOrderCount->where('status', 'submitted');
            }

            // Apply timeliness filter
            if ($timeliness === 'late') {
                $weeklyCount->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id')
                    ->whereRaw('weekly_reports.created_at > report_types.deadline');
                $monthlyCount->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id')
                    ->whereRaw('monthly_reports.created_at > report_types.deadline');
                $quarterlyCount->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id')
                    ->whereRaw('quarterly_reports.created_at > report_types.deadline');
                $semestralCount->join('report_types', 'semestral_reports.report_type_id', '=', 'report_types.id')
                    ->whereRaw('semestral_reports.created_at > report_types.deadline');
                $annualCount->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id')
                    ->whereRaw('annual_reports.created_at > report_types.deadline');
                $executiveOrderCount->join('report_types', 'executive_orders.report_type_id', '=', 'report_types.id')
                    ->whereRaw('executive_orders.created_at > report_types.deadline');
            }

            $reportTypeData = [
                $weeklyCount->count(),
                $monthlyCount->count(),
                $quarterlyCount->count(),
                $semestralCount->count(),
                $annualCount->count(),
                $executiveOrderCount->count()
            ];
        } else {
            $reportTypeData = [0, 0, 0, 0, 0, 0];
        }

        return [
            'submissionsByMonth' => $submissionsByMonth,
            'reportTypeData' => $reportTypeData
        ];
    }

    /**
     * Get cluster submissions data with filters applied
     */
    private function getClusterSubmissions($clusterIds, $reportType = null, $status = null, $timeliness = null)
    {
        $clusterSubmissions = [];
        $clusters = Cluster::whereIn('id', $clusterIds)->get();

        foreach ($clusters as $cluster) {
            $barangayIds = User::where('user_type', 'barangay')
                ->where('cluster_id', $cluster->id)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            $submissionCount = 0;

            if (!empty($barangayIds)) {
                // Create queries for each report type (use DISTINCT to prevent counting resubmissions)
                $weeklyQuery = DB::table('weekly_reports')
                    ->select('user_id', 'report_type_id')
                    ->whereIn('user_id', $barangayIds)
                    ->distinct();
                $monthlyQuery = DB::table('monthly_reports')
                    ->select('user_id', 'report_type_id')
                    ->whereIn('user_id', $barangayIds)
                    ->distinct();
                $quarterlyQuery = DB::table('quarterly_reports')
                    ->select('user_id', 'report_type_id')
                    ->whereIn('user_id', $barangayIds)
                    ->distinct();
                $semestralQuery = DB::table('semestral_reports')
                    ->select('user_id', 'report_type_id')
                    ->whereIn('user_id', $barangayIds)
                    ->distinct();
                $annualQuery = DB::table('annual_reports')
                    ->select('user_id', 'report_type_id')
                    ->whereIn('user_id', $barangayIds)
                    ->distinct();

                // Apply status filter
                if ($status === 'submitted') {
                    $weeklyQuery->where('status', 'submitted');
                    $monthlyQuery->where('status', 'submitted');
                    $quarterlyQuery->where('status', 'submitted');
                    $semestralQuery->where('status', 'submitted');
                    $annualQuery->where('status', 'submitted');
                } elseif ($status !== 'no_submission') {
                    // Default: only show submitted reports
                    $weeklyQuery->where('status', 'submitted');
                    $monthlyQuery->where('status', 'submitted');
                    $quarterlyQuery->where('status', 'submitted');
                    $semestralQuery->where('status', 'submitted');
                    $annualQuery->where('status', 'submitted');
                }

                // Apply timeliness filter
                if ($timeliness === 'late') {
                    $weeklyQuery->join('report_types', 'weekly_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('weekly_reports.created_at > report_types.deadline');
                    $monthlyQuery->join('report_types', 'monthly_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('monthly_reports.created_at > report_types.deadline');
                    $quarterlyQuery->join('report_types', 'quarterly_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('quarterly_reports.created_at > report_types.deadline');
                    $semestralQuery->join('report_types', 'semestral_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('semestral_reports.created_at > report_types.deadline');
                    $annualQuery->join('report_types', 'annual_reports.report_type_id', '=', 'report_types.id')
                        ->whereRaw('annual_reports.created_at > report_types.deadline');
                }

                // Apply report type filter
                if ($reportType) {
                    if ($reportType != 'weekly') $weeklyQuery->whereRaw('1=0');
                    if ($reportType != 'monthly') $monthlyQuery->whereRaw('1=0');
                    if ($reportType != 'quarterly') $quarterlyQuery->whereRaw('1=0');
                    if ($reportType != 'semestral') $semestralQuery->whereRaw('1=0');
                    if ($reportType != 'annual') $annualQuery->whereRaw('1=0');
                }

                $submissionCount = $weeklyQuery->count() +
                                 $monthlyQuery->count() +
                                 $quarterlyQuery->count() +
                                 $semestralQuery->count() +
                                 $annualQuery->count();
            }

            $clusterSubmissions[$cluster->id] = $submissionCount;
        }

        return $clusterSubmissions;
    }

    /**
     * Get barangay summary data for dashboard
     */
    private function getBarangaySummary($barangays)
    {
        $summary = [];

        foreach ($barangays as $barangay) {
            // Get all reports for this barangay with relationships
            $weeklyReports = WeeklyReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $monthlyReports = MonthlyReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $quarterlyReports = QuarterlyReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $semestralReports = SemestralReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $annualReports = AnnualReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();

            $allReports = collect()
                ->merge($weeklyReports)
                ->merge($monthlyReports)
                ->merge($quarterlyReports)
                ->merge($semestralReports)
                ->merge($annualReports);

            // Group by report_type_id to get only latest submission for each report type
            $uniqueReports = collect();
            $groupedReports = $allReports->groupBy('report_type_id');

            foreach ($groupedReports as $group) {
                // Get the latest submission for this report type
                $latestReport = $group->sortByDesc('created_at')->first();
                if ($latestReport) {
                    $uniqueReports->push($latestReport);
                }
            }

            $totalReports = $uniqueReports->count();
            $onTime = 0;
            $late = 0;

            // Calculate on-time vs late submissions using unique reports only
            foreach ($uniqueReports as $report) {
                if (isset($report->reportType) && $report->reportType->deadline) {
                    if (Carbon::parse($report->created_at)->isAfter($report->reportType->deadline)) {
                        $late++;
                    } else {
                        $onTime++;
                    }
                } else {
                    // If no deadline info, assume on time
                    $onTime++;
                }
            }

            // Calculate expected reports vs actual submissions
            // Count unique report types that have been submitted (not total submissions)
            $uniqueSubmittedReportTypes = $allReports->pluck('report_type_id')->unique()->count();
            $totalReportTypes = ReportType::active()->where('deadline', '>=', now())->count();
            $noSubmission = max(0, $totalReportTypes - $uniqueSubmittedReportTypes);

            // Get cluster information
            $cluster = Cluster::find($barangay->cluster_id);

            $summary[] = [
                'id' => $barangay->id,
                'name' => $barangay->name,
                'email' => $barangay->email,
                'cluster_id' => $barangay->cluster_id,
                'cluster_name' => $cluster ? $cluster->name : 'No Cluster',
                'total_reports' => $totalReports,
                'on_time' => $onTime,
                'late' => $late,
                'no_submission' => $noSubmission,
                'pending_submissions' => $noSubmission  // Add the new key for consistency
            ];
        }

        return $summary;
    }

    /**
     * Get categorized barangays for submission lists
     */
    private function getSubmissionCategories($barangays)
    {
        $onTimeBarangays = [];
        $lateBarangays = [];
        $noSubmissionBarangays = [];

        foreach ($barangays as $barangay) {
            // Get cluster information
            $cluster = Cluster::find($barangay->cluster_id);
            $clusterName = $cluster ? $cluster->name : 'No Cluster';

            // Get all reports for this barangay with relationships
            $weeklyReports = WeeklyReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $monthlyReports = MonthlyReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $quarterlyReports = QuarterlyReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $semestralReports = SemestralReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();
            $annualReports = AnnualReport::with('reportType')->where('user_id', $barangay->id)->where('status', 'submitted')->get();

            $allReports = collect()
                ->merge($weeklyReports)
                ->merge($monthlyReports)
                ->merge($quarterlyReports)
                ->merge($semestralReports)
                ->merge($annualReports);

            // Group by report_type_id to get only latest submission for each report type
            $uniqueReports = collect();
            $groupedReports = $allReports->groupBy('report_type_id');

            foreach ($groupedReports as $group) {
                // Get the latest submission for this report type
                $latestReport = $group->sortByDesc('created_at')->first();
                if ($latestReport) {
                    $uniqueReports->push($latestReport);
                }
            }

            $totalReports = $uniqueReports->count();
            $onTimeCount = 0;
            $lateCount = 0;

            // Calculate on-time vs late submissions using unique reports only
            foreach ($uniqueReports as $report) {
                if (isset($report->reportType) && $report->reportType->deadline) {
                    if (Carbon::parse($report->created_at)->isAfter($report->reportType->deadline)) {
                        $lateCount++;
                    } else {
                        $onTimeCount++;
                    }
                } else {
                    // If no deadline info, assume on time
                    $onTimeCount++;
                }
            }

            // Calculate pending submissions (how many report types with future deadlines this barangay hasn't submitted)
            $uniqueSubmittedReportTypes = $allReports->pluck('report_type_id')->unique()->count();
            $totalReportTypes = ReportType::active()->where('deadline', '>=', now())->count();
            $pendingSubmissions = max(0, $totalReportTypes - $uniqueSubmittedReportTypes);

            $barangayData = [
                'id' => $barangay->id,
                'name' => $barangay->name,
                'email' => $barangay->email,
                'cluster_name' => $clusterName,
                'total_reports' => $totalReports,
                'on_time_count' => $onTimeCount,
                'late_count' => $lateCount,
                'pending_submissions' => $pendingSubmissions
            ];

            // Categorize barangays based on submission status
            // Barangays can appear in multiple categories

            // Add to "On Time Submissions" if they have on-time submissions
            if ($onTimeCount > 0) {
                $onTimeBarangays[] = $barangayData;
            }

            // Add to "Late Submissions" if they have late submissions
            if ($lateCount > 0) {
                $lateBarangays[] = $barangayData;
            }

            // Add to "No Submissions" if they have pending submissions
            if ($pendingSubmissions > 0) {
                $noSubmissionBarangays[] = $barangayData;
            }
        }

        return [
            'onTimeBarangays' => $onTimeBarangays,
            'lateBarangays' => $lateBarangays,
            'noSubmissionBarangays' => $noSubmissionBarangays
        ];
    }

    /**
     * Get recent submissions using the same logic as view-submissions
     */
    private function getRecentSubmissions($barangayIds)
    {
        // Initialize queries with relationships - only for facilitator's clusters
        $weeklyQuery = WeeklyReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $monthlyQuery = MonthlyReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $quarterlyQuery = QuarterlyReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $semestralQuery = SemestralReport::with(['user', 'reportType'])
            ->whereIn('user_id', $barangayIds);
        $annualQuery = AnnualReport::with(['user', 'reportType'])
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

        // Group reports by report name to show unique report types regardless of barangay or report_type_id
        $latestReports = collect();
        $groupedReports = $allReports->groupBy('report_name');

        foreach ($groupedReports as $group) {
            // Sort by created_at in descending order and take the first one (latest)
            $latestReport = $group->sortByDesc('created_at')->first();
            if ($latestReport) {
                $latestReports->push($latestReport);
            }
        }

        // Sort the filtered collection by created_at in descending order and take 5 most recent
        return $latestReports->sortByDesc('created_at')->take(5);
    }

    /**
     * Calculate the next deadline for a report type.
     *
     * @param \App\Models\ReportType $reportType
     * @return \Carbon\Carbon|null
     */
    private function calculateNextDeadline($reportType)
    {
        $now = Carbon::now();
        $deadline = null;

        switch ($reportType->frequency) {
            case 'weekly':
                // Assuming weekly reports are due every Friday
                $deadline = $now->copy()->next(Carbon::FRIDAY);
                break;

            case 'monthly':
                // Assuming monthly reports are due on the 5th of next month
                $deadline = $now->copy()->addMonth()->startOfMonth()->addDays(4);
                break;

            case 'quarterly':
                // Assuming quarterly reports are due on the 15th of the month after the quarter ends
                $currentQuarter = ceil($now->month / 3);
                $quarterEndMonth = $currentQuarter * 3;
                $deadline = $now->copy()->month($quarterEndMonth)->endOfMonth()->addDays(15);
                if ($deadline->isPast()) {
                    $deadline = $now->copy()->month($quarterEndMonth + 3)->endOfMonth()->addDays(15);
                }
                break;

            case 'annual':
                // Assuming annual reports are due on January 15th of the next year
                $deadline = $now->copy()->addYear()->startOfYear()->addDays(14);
                if ($now->month == 12 && $now->day > 15) {
                    $deadline = $now->copy()->addYear()->startOfYear()->addDays(14);
                } else if ($now->month == 1 && $now->day < 15) {
                    $deadline = $now->copy()->startOfYear()->addDays(14);
                } else {
                    $deadline = $now->copy()->addYear()->startOfYear()->addDays(14);
                }
                break;
        }

        return $deadline;
    }



    /**
     * Show the view submissions page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewSubmissions(Request $request)
    {
        try {
        /** @var \App\Models\User $facilitator */
        $facilitator = Auth::user();

        // Get clusters assigned to the facilitator
        if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
            $clusterIds = $facilitator->assignedClusters()->pluck('clusters.id')->toArray();
        } else {
            // Fallback: get all clusters if the relationship doesn't exist
            $clusterIds = DB::table('clusters')->where('is_active', true)->pluck('id')->toArray();
        }

        // Get barangays in those clusters for the filter dropdown
        $barangays = User::where('user_type', 'barangay')
            ->whereIn('cluster_id', $clusterIds)
            ->where('is_active', true)
            ->get();

        // Get barangay IDs for report filtering
        $barangayIds = $barangays->pluck('id')->toArray();

            $perPage = $request->get('per_page', 10);
            $selectedBarangay = null;

            // Initialize queries with relationships - only for facilitator's clusters
            $weeklyQuery = WeeklyReport::with(['user', 'reportType'])
                ->whereIn('user_id', $barangayIds);
            $monthlyQuery = MonthlyReport::with(['user', 'reportType'])
                ->whereIn('user_id', $barangayIds);
            $quarterlyQuery = QuarterlyReport::with(['user', 'reportType'])
                ->whereIn('user_id', $barangayIds);
            $semestralQuery = SemestralReport::with(['user', 'reportType'])
                ->whereIn('user_id', $barangayIds);
            $annualQuery = AnnualReport::with(['user', 'reportType'])
                ->whereIn('user_id', $barangayIds);

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
                return view('facilitator.partials.submissions-table-container', compact('reports', 'selectedBarangay'))->render();
            }

            // Return the full view for non-AJAX requests
            return view('facilitator.view-submissions', compact('reports', 'barangays', 'selectedBarangay'));
        } catch (\Exception $e) {
            Log::error('Error in facilitator view submissions: ' . $e->getMessage());
            return view('facilitator.view-submissions', [
                'reports' => collect(),
                'barangays' => User::where('user_type', 'barangay')->where('is_active', true)->get(),
                'selectedBarangay' => null
            ])->with('error', 'An error occurred while loading submissions: ' . $e->getMessage());
        }
    }

    /**
     * Get files for a report.
     *
     * @param mixed $report
     * @return array
     */
    private function getReportFiles($report)
    {
        if (!$report->file_path) {
            return [];
        }

        return [
            [
                'id' => $report->id,
                'original_name' => $report->file_name ?? basename($report->file_path),
                'file_path' => $report->file_path
            ]
        ];
    }

    /**
     * Add remarks to a report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addRemarks(Request $request, $id)
    {

        /** @var \App\Models\User $facilitator */
        $facilitator = Auth::user();

        // Check if the assignedClusters relationship exists and is callable
        if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
            $clusterIds = $facilitator->assignedClusters()->pluck('clusters.id')->toArray();
        } else {
            // Fallback: get all clusters if the relationship doesn't exist
            $clusterIds = DB::table('clusters')->where('is_active', true)->pluck('id')->toArray();
        }

        // Get barangays in those clusters
        $barangayIds = User::where('user_type', 'barangay')
            ->whereIn('cluster_id', $clusterIds)
            ->where('is_active', true)
            ->pluck('id');

        $request->validate([
            'remarks' => 'nullable|string',
            'type' => 'required|in:weekly,monthly,quarterly,semestral,annual',
            'can_update' => 'nullable|boolean',
        ]);

        $reportClass = null;
        switch ($request->type) {
            case 'weekly':
                $reportClass = WeeklyReport::class;
                break;
            case 'monthly':
                $reportClass = MonthlyReport::class;
                break;
            case 'quarterly':
                $reportClass = QuarterlyReport::class;
                break;
            case 'semestral':
                $reportClass = SemestralReport::class;
                break;
            case 'annual':
                $reportClass = AnnualReport::class;
                break;
        }

        $report = $reportClass::with('user')->findOrFail($id);

        // Verify the report belongs to a barangay in the facilitator's clusters
        if (!$barangayIds->contains($report->user_id)) {
            return back()->with('error', 'You can only add remarks to reports from barangays in your assigned clusters.');
        }

        // Update the remarks and can_update status
        $updateData = [
            'remarks' => $request->remarks
        ];

        // Handle can_update checkbox
        if ($request->has('can_update')) {
            $updateData['can_update'] = $request->boolean('can_update');
        } else {
            $updateData['can_update'] = false;
        }

        $report->update($updateData);

        // Send notification to barangay user if remarks were added
        if (!empty($request->remarks)) {
            $barangayUser = $report->user;
            $facilitatorName = Auth::user()->name;

            if ($barangayUser) {
                try {
                    // Send notification
                    $barangayUser->notify(new ReportRemarksNotification(
                        $report,
                        $request->remarks,
                        $request->type,
                        $facilitatorName
                    ));

                    Log::info('Facilitator notification sent to barangay user', [
                        'facilitator_id' => Auth::id(),
                        'facilitator_name' => $facilitatorName,
                        'barangay_id' => $barangayUser->id,
                        'barangay_email' => $barangayUser->email,
                        'report_id' => $report->id,
                        'report_type' => $request->type,
                        'remarks' => $request->remarks,
                        'can_update' => $request->boolean('can_update')
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send facilitator notification to barangay user', [
                        'facilitator_id' => Auth::id(),
                        'barangay_id' => $barangayUser->id,
                        'barangay_email' => $barangayUser->email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::warning('Barangay user not found for facilitator report', [
                    'facilitator_id' => Auth::id(),
                    'report_id' => $report->id,
                    'user_id' => $report->user_id
                ]);
            }
        }

        return back()->with('success', 'Remarks and update permissions saved successfully.');
    }

    /**
     * Download or view a file from a report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($id)
    {
        try {
            /** @var \App\Models\User $facilitator */
            $facilitator = Auth::user();

            // Get clusters assigned to the facilitator
            if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
                $clusterIds = $facilitator->assignedClusters()->pluck('clusters.id')->toArray();
            } else {
                // Fallback: get all clusters if the relationship doesn't exist
                $clusterIds = DB::table('clusters')->where('is_active', true)->pluck('id')->toArray();
            }

            // Get barangays in those clusters
            $barangayIds = User::where('user_type', 'barangay')
                ->whereIn('cluster_id', $clusterIds)
                ->where('is_active', true)
                ->pluck('id');

            // Try to find the report in each table - only from facilitator's assigned clusters
            $report = WeeklyReport::whereIn('user_id', $barangayIds)
                ->where('id', $id)
                ->first();

            if (!$report) {
                $report = MonthlyReport::whereIn('user_id', $barangayIds)
                    ->where('id', $id)
                    ->first();
            }

            if (!$report) {
                $report = QuarterlyReport::whereIn('user_id', $barangayIds)
                    ->where('id', $id)
                    ->first();
            }

            if (!$report) {
                $report = SemestralReport::whereIn('user_id', $barangayIds)
                    ->where('id', $id)
                    ->first();
            }

            if (!$report) {
                $report = AnnualReport::whereIn('user_id', $barangayIds)
                    ->where('id', $id)
                    ->first();
            }

            if (!$report) {
                return response()->json(['error' => 'Report not found or access denied.'], 404);
            }

            if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
                return response()->json(['error' => 'File not found.'], 404);
            }

            // Get the full path to the file
            $path = Storage::disk('public')->path($report->file_path);

            // Get the file's mime type
            $mimeType = mime_content_type($path);
            $fileName = basename($report->file_path);

            Log::info('Facilitator file access', [
                'facilitator_id' => $facilitator->id,
                'report_id' => $id,
                'path' => $path,
                'mime_type' => $mimeType,
                'file_name' => $fileName,
                'is_download' => request()->has('download')
            ]);

            // If it's a download request, force download
            if (request()->has('download')) {
                return response()->download($path, $fileName);
            }

            // Define viewable types
            $viewableTypes = [
                'application/pdf',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/webp',
                'text/plain',
                'text/html',
                'text/csv',
                'application/json'
            ];

            // For viewable types, return the file for inline viewing
            if (in_array($mimeType, $viewableTypes)) {
                return response()->file($path, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                    'Cache-Control' => 'public, max-age=0',
                    'Accept-Ranges' => 'bytes'
                ]);
            }

            // For non-viewable types, force download
            return response()->download($path, $fileName);
        } catch (\Exception $e) {
            Log::error('Error in facilitator downloadFile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'facilitator_id' => Auth::id(),
                'report_id' => $id
            ]);
            return response()->json(['error' => 'Error accessing file: ' . $e->getMessage()], 500);
        }
    }
}
