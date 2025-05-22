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
     * Display the admin dashboard with charts and statistics.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        $reportType = $request->input('report_type');
        $clusterId = $request->input('cluster_id');
        $search = $request->input('search');

        // Count total report types created by admin
        $totalReportTypes = ReportType::count();

        // Count total barangays in the system
        $barangayQuery = User::where(function($query) {
            $query->where('role', 'barangay')
                  ->orWhere('user_type', 'barangay');
        });

        // Apply cluster filter to barangays if specified
        if ($clusterId) {
            $barangayQuery->where('cluster_id', $clusterId);
        }

        $totalBarangays = $barangayQuery->count();

        // Calculate the total expected reports (report types × barangays)
        $totalExpectedReports = $totalReportTypes * $totalBarangays;

        // Get unique submitted reports (count each report type per barangay only once)
        $weeklyQuery = DB::table('weekly_reports')
            ->select('user_id', 'report_type_id')
            ->where('status', 'submitted');

        $monthlyQuery = DB::table('monthly_reports')
            ->select('user_id', 'report_type_id')
            ->where('status', 'submitted');

        $quarterlyQuery = DB::table('quarterly_reports')
            ->select('user_id', 'report_type_id')
            ->where('status', 'submitted');

        $semestralQuery = DB::table('semestral_reports')
            ->select('user_id', 'report_type_id')
            ->where('status', 'submitted');

        $annualQuery = DB::table('annual_reports')
            ->select('user_id', 'report_type_id')
            ->where('status', 'submitted');

        // Apply date range filter if specified
        if ($startDate && $endDate) {
            $weeklyQuery->whereBetween('created_at', [$startDate, $endDate]);
            $monthlyQuery->whereBetween('created_at', [$startDate, $endDate]);
            $quarterlyQuery->whereBetween('created_at', [$startDate, $endDate]);
            $semestralQuery->whereBetween('created_at', [$startDate, $endDate]);
            $annualQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply cluster filter if specified
        if ($clusterId) {
            $barangayIds = User::where('cluster_id', $clusterId)
                ->where(function($query) {
                    $query->where('role', 'barangay')
                          ->orWhere('user_type', 'barangay');
                })
                ->pluck('id')
                ->toArray();

            if (!empty($barangayIds)) {
                $weeklyQuery->whereIn('user_id', $barangayIds);
                $monthlyQuery->whereIn('user_id', $barangayIds);
                $quarterlyQuery->whereIn('user_id', $barangayIds);
                $semestralQuery->whereIn('user_id', $barangayIds);
                $annualQuery->whereIn('user_id', $barangayIds);
            }
        }

        // Apply search filter if specified
        if ($search) {
            // Get report types matching the search
            $reportTypeIds = ReportType::where('name', 'like', "%{$search}%")
                ->pluck('id')
                ->toArray();

            // Get barangays matching the search
            $barangayIds = User::where('name', 'like', "%{$search}%")
                ->where(function($query) {
                    $query->where('role', 'barangay')
                          ->orWhere('user_type', 'barangay');
                })
                ->pluck('id')
                ->toArray();

            if (!empty($reportTypeIds) || !empty($barangayIds)) {
                $weeklyQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $monthlyQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $quarterlyQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $semestralQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $annualQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });
            }
        }

        // Apply report type filter if specified
        if ($reportType) {
            // Only count the specified report type
            if ($reportType != 'weekly') $weeklyQuery->whereRaw('1=0');
            if ($reportType != 'monthly') $monthlyQuery->whereRaw('1=0');
            if ($reportType != 'quarterly') $quarterlyQuery->whereRaw('1=0');
            if ($reportType != 'semestral') $semestralQuery->whereRaw('1=0');
            if ($reportType != 'annual') $annualQuery->whereRaw('1=0');
        }

        // Execute the queries
        $weeklySubmitted = $weeklyQuery->groupBy('user_id', 'report_type_id')->get()->count();
        $monthlySubmitted = $monthlyQuery->groupBy('user_id', 'report_type_id')->get()->count();
        $quarterlySubmitted = $quarterlyQuery->groupBy('user_id', 'report_type_id')->get()->count();
        $semestralSubmitted = $semestralQuery->groupBy('user_id', 'report_type_id')->get()->count();
        $annualSubmitted = $annualQuery->groupBy('user_id', 'report_type_id')->get()->count();

        // Total submitted reports (counting each report type per barangay only once)
        $totalSubmittedReports = $weeklySubmitted + $monthlySubmitted + $quarterlySubmitted + $semestralSubmitted + $annualSubmitted;

        // Calculate no submissions (expected - submitted)
        $noSubmissionReports = $totalExpectedReports - $totalSubmittedReports;

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

        // Apply date range filter if specified
        if ($startDate && $endDate) {
            $weeklyLateQuery->whereBetween('weekly_reports.created_at', [$startDate, $endDate]);
            $monthlyLateQuery->whereBetween('monthly_reports.created_at', [$startDate, $endDate]);
            $quarterlyLateQuery->whereBetween('quarterly_reports.created_at', [$startDate, $endDate]);
            $semestralLateQuery->whereBetween('semestral_reports.created_at', [$startDate, $endDate]);
            $annualLateQuery->whereBetween('annual_reports.created_at', [$startDate, $endDate]);
        }

        // Apply cluster filter if specified
        if ($clusterId) {
            $barangayIds = User::where('cluster_id', $clusterId)
                ->where(function($query) {
                    $query->where('role', 'barangay')
                          ->orWhere('user_type', 'barangay');
                })
                ->pluck('id')
                ->toArray();

            if (!empty($barangayIds)) {
                $weeklyLateQuery->whereIn('weekly_reports.user_id', $barangayIds);
                $monthlyLateQuery->whereIn('monthly_reports.user_id', $barangayIds);
                $quarterlyLateQuery->whereIn('quarterly_reports.user_id', $barangayIds);
                $semestralLateQuery->whereIn('semestral_reports.user_id', $barangayIds);
                $annualLateQuery->whereIn('annual_reports.user_id', $barangayIds);
            }
        }

        // Apply search filter if specified
        if ($search) {
            // Get report types matching the search
            $reportTypeIds = ReportType::where('name', 'like', "%{$search}%")
                ->pluck('id')
                ->toArray();

            // Get barangays matching the search
            $barangayIds = User::where('name', 'like', "%{$search}%")
                ->where(function($query) {
                    $query->where('role', 'barangay')
                          ->orWhere('user_type', 'barangay');
                })
                ->pluck('id')
                ->toArray();

            if (!empty($reportTypeIds) || !empty($barangayIds)) {
                $weeklyLateQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('weekly_reports.report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('weekly_reports.user_id', $barangayIds);
                    }
                });

                $monthlyLateQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('monthly_reports.report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('monthly_reports.user_id', $barangayIds);
                    }
                });

                $quarterlyLateQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('quarterly_reports.report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('quarterly_reports.user_id', $barangayIds);
                    }
                });

                $semestralLateQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('semestral_reports.report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('semestral_reports.user_id', $barangayIds);
                    }
                });

                $annualLateQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('annual_reports.report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('annual_reports.user_id', $barangayIds);
                    }
                });
            }
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

        // Get submissions by frequency for chart
        $weeklyCountQuery = WeeklyReport::where('status', 'submitted');
        $monthlyCountQuery = MonthlyReport::where('status', 'submitted');
        $quarterlyCountQuery = QuarterlyReport::where('status', 'submitted');
        $semestralCountQuery = SemestralReport::where('status', 'submitted');
        $annualCountQuery = AnnualReport::where('status', 'submitted');

        // Apply date range filter if specified
        if ($startDate && $endDate) {
            $weeklyCountQuery->whereBetween('created_at', [$startDate, $endDate]);
            $monthlyCountQuery->whereBetween('created_at', [$startDate, $endDate]);
            $quarterlyCountQuery->whereBetween('created_at', [$startDate, $endDate]);
            $semestralCountQuery->whereBetween('created_at', [$startDate, $endDate]);
            $annualCountQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply cluster filter if specified
        if ($clusterId) {
            $barangayIds = User::where('cluster_id', $clusterId)
                ->where(function($query) {
                    $query->where('role', 'barangay')
                          ->orWhere('user_type', 'barangay');
                })
                ->pluck('id')
                ->toArray();

            if (!empty($barangayIds)) {
                $weeklyCountQuery->whereIn('user_id', $barangayIds);
                $monthlyCountQuery->whereIn('user_id', $barangayIds);
                $quarterlyCountQuery->whereIn('user_id', $barangayIds);
                $semestralCountQuery->whereIn('user_id', $barangayIds);
                $annualCountQuery->whereIn('user_id', $barangayIds);
            }
        }

        // Apply search filter if specified
        if ($search) {
            // Get report types matching the search
            $reportTypeIds = ReportType::where('name', 'like', "%{$search}%")
                ->pluck('id')
                ->toArray();

            // Get barangays matching the search
            $barangayIds = User::where('name', 'like', "%{$search}%")
                ->where(function($query) {
                    $query->where('role', 'barangay')
                          ->orWhere('user_type', 'barangay');
                })
                ->pluck('id')
                ->toArray();

            if (!empty($reportTypeIds) || !empty($barangayIds)) {
                $weeklyCountQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $monthlyCountQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $quarterlyCountQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $semestralCountQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });

                $annualCountQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                    if (!empty($reportTypeIds)) {
                        $query->whereIn('report_type_id', $reportTypeIds);
                    }
                    if (!empty($barangayIds)) {
                        $query->orWhereIn('user_id', $barangayIds);
                    }
                });
            }
        }

        // Execute the queries
        $weeklyCount = $weeklyCountQuery->count();
        $monthlyCount = $monthlyCountQuery->count();
        $quarterlyCount = $quarterlyCountQuery->count();
        $semestralCount = $semestralCountQuery->count();
        $annualCount = $annualCountQuery->count();

        // Get submissions by month for trend chart
        $submissionsByMonth = [];
        $currentYear = date('Y');

        // Get the year from the date filter if specified
        if ($startDate && $endDate) {
            // If the date range spans multiple years, use the year from the start date
            $currentYear = $startDate->year;
        }

        for ($month = 1; $month <= 12; $month++) {
            $monthStartDate = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $monthEndDate = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();

            // Skip months outside the filtered date range if specified
            if ($startDate && $endDate) {
                if ($monthEndDate->lt($startDate) || $monthStartDate->gt($endDate)) {
                    $submissionsByMonth[] = 0;
                    continue;
                }
            }

            // Create queries for each report type
            $weeklyMonthQuery = WeeklyReport::where('status', 'submitted')
                ->whereBetween('created_at', [$monthStartDate, $monthEndDate]);

            $monthlyMonthQuery = MonthlyReport::where('status', 'submitted')
                ->whereBetween('created_at', [$monthStartDate, $monthEndDate]);

            $quarterlyMonthQuery = QuarterlyReport::where('status', 'submitted')
                ->whereBetween('created_at', [$monthStartDate, $monthEndDate]);

            $semestralMonthQuery = SemestralReport::where('status', 'submitted')
                ->whereBetween('created_at', [$monthStartDate, $monthEndDate]);

            $annualMonthQuery = AnnualReport::where('status', 'submitted')
                ->whereBetween('created_at', [$monthStartDate, $monthEndDate]);

            // Apply cluster filter if specified
            if ($clusterId) {
                $barangayIds = User::where('cluster_id', $clusterId)
                    ->where(function($query) {
                        $query->where('role', 'barangay')
                              ->orWhere('user_type', 'barangay');
                    })
                    ->pluck('id')
                    ->toArray();

                if (!empty($barangayIds)) {
                    $weeklyMonthQuery->whereIn('user_id', $barangayIds);
                    $monthlyMonthQuery->whereIn('user_id', $barangayIds);
                    $quarterlyMonthQuery->whereIn('user_id', $barangayIds);
                    $semestralMonthQuery->whereIn('user_id', $barangayIds);
                    $annualMonthQuery->whereIn('user_id', $barangayIds);
                }
            }

            // Apply search filter if specified
            if ($search) {
                // Get report types matching the search
                $reportTypeIds = ReportType::where('name', 'like', "%{$search}%")
                    ->pluck('id')
                    ->toArray();

                // Get barangays matching the search
                $barangayIds = User::where('name', 'like', "%{$search}%")
                    ->where(function($query) {
                        $query->where('role', 'barangay')
                              ->orWhere('user_type', 'barangay');
                    })
                    ->pluck('id')
                    ->toArray();

                if (!empty($reportTypeIds) || !empty($barangayIds)) {
                    $weeklyMonthQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                        if (!empty($reportTypeIds)) {
                            $query->whereIn('report_type_id', $reportTypeIds);
                        }
                        if (!empty($barangayIds)) {
                            $query->orWhereIn('user_id', $barangayIds);
                        }
                    });

                    $monthlyMonthQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                        if (!empty($reportTypeIds)) {
                            $query->whereIn('report_type_id', $reportTypeIds);
                        }
                        if (!empty($barangayIds)) {
                            $query->orWhereIn('user_id', $barangayIds);
                        }
                    });

                    $quarterlyMonthQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                        if (!empty($reportTypeIds)) {
                            $query->whereIn('report_type_id', $reportTypeIds);
                        }
                        if (!empty($barangayIds)) {
                            $query->orWhereIn('user_id', $barangayIds);
                        }
                    });

                    $semestralMonthQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                        if (!empty($reportTypeIds)) {
                            $query->whereIn('report_type_id', $reportTypeIds);
                        }
                        if (!empty($barangayIds)) {
                            $query->orWhereIn('user_id', $barangayIds);
                        }
                    });

                    $annualMonthQuery->where(function($query) use ($reportTypeIds, $barangayIds) {
                        if (!empty($reportTypeIds)) {
                            $query->whereIn('report_type_id', $reportTypeIds);
                        }
                        if (!empty($barangayIds)) {
                            $query->orWhereIn('user_id', $barangayIds);
                        }
                    });
                }
            }

            // Apply report type filter if specified
            if ($reportType) {
                // Only count the specified report type
                if ($reportType != 'weekly') $weeklyMonthQuery->whereRaw('1=0');
                if ($reportType != 'monthly') $monthlyMonthQuery->whereRaw('1=0');
                if ($reportType != 'quarterly') $quarterlyMonthQuery->whereRaw('1=0');
                if ($reportType != 'semestral') $semestralMonthQuery->whereRaw('1=0');
                if ($reportType != 'annual') $annualMonthQuery->whereRaw('1=0');
            }

            $monthlyTotal =
                $weeklyMonthQuery->count() +
                $monthlyMonthQuery->count() +
                $quarterlyMonthQuery->count() +
                $semestralMonthQuery->count() +
                $annualMonthQuery->count();

            $submissionsByMonth[] = $monthlyTotal;
        }

        // Get top 5 barangays with most submissions
        $barangaySubmissions = [];
        $barangayQuery = User::where(function($query) {
            $query->where('role', 'barangay')
                  ->orWhere('user_type', 'barangay');
        });

        // Apply cluster filter to barangays if specified
        if ($clusterId) {
            $barangayQuery->where('cluster_id', $clusterId);
        }

        // Apply search filter to barangays if specified
        if ($search) {
            $barangayQuery->where('name', 'like', "%{$search}%");
        }

        $barangays = $barangayQuery->get();

        foreach ($barangays as $barangay) {
            // Create queries for each report type
            $weeklyBarangayQuery = WeeklyReport::where('user_id', $barangay->id)
                ->where('status', 'submitted');

            $monthlyBarangayQuery = MonthlyReport::where('user_id', $barangay->id)
                ->where('status', 'submitted');

            $quarterlyBarangayQuery = QuarterlyReport::where('user_id', $barangay->id)
                ->where('status', 'submitted');

            $semestralBarangayQuery = SemestralReport::where('user_id', $barangay->id)
                ->where('status', 'submitted');

            $annualBarangayQuery = AnnualReport::where('user_id', $barangay->id)
                ->where('status', 'submitted');

            // Apply date range filter if specified
            if ($startDate && $endDate) {
                $weeklyBarangayQuery->whereBetween('created_at', [$startDate, $endDate]);
                $monthlyBarangayQuery->whereBetween('created_at', [$startDate, $endDate]);
                $quarterlyBarangayQuery->whereBetween('created_at', [$startDate, $endDate]);
                $semestralBarangayQuery->whereBetween('created_at', [$startDate, $endDate]);
                $annualBarangayQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Apply search filter for report types if specified
            if ($search) {
                $reportTypeIds = ReportType::where('name', 'like', "%{$search}%")
                    ->pluck('id')
                    ->toArray();

                if (!empty($reportTypeIds)) {
                    $weeklyBarangayQuery->whereIn('report_type_id', $reportTypeIds);
                    $monthlyBarangayQuery->whereIn('report_type_id', $reportTypeIds);
                    $quarterlyBarangayQuery->whereIn('report_type_id', $reportTypeIds);
                    $semestralBarangayQuery->whereIn('report_type_id', $reportTypeIds);
                    $annualBarangayQuery->whereIn('report_type_id', $reportTypeIds);
                }
            }

            // Apply report type filter if specified
            if ($reportType) {
                // Only count the specified report type
                if ($reportType != 'weekly') $weeklyBarangayQuery->whereRaw('1=0');
                if ($reportType != 'monthly') $monthlyBarangayQuery->whereRaw('1=0');
                if ($reportType != 'quarterly') $quarterlyBarangayQuery->whereRaw('1=0');
                if ($reportType != 'semestral') $semestralBarangayQuery->whereRaw('1=0');
                if ($reportType != 'annual') $annualBarangayQuery->whereRaw('1=0');
            }

            $submissionCount =
                $weeklyBarangayQuery->count() +
                $monthlyBarangayQuery->count() +
                $quarterlyBarangayQuery->count() +
                $semestralBarangayQuery->count() +
                $annualBarangayQuery->count();

            $barangaySubmissions[$barangay->name] = $submissionCount;
        }

        // Sort by submission count (descending) and take top 5
        arsort($barangaySubmissions);
        $topBarangays = array_slice($barangaySubmissions, 0, 5, true);

        // Get submissions per cluster
        $clusterSubmissions = [];
        $clusterQuery = Cluster::query();

        // Apply cluster filter if specified
        if ($clusterId) {
            $clusterQuery->where('id', $clusterId);
        }

        $clusters = $clusterQuery->get();

        foreach ($clusters as $cluster) {
            // Get all barangays in this cluster
            $clusterBarangays = User::where('cluster_id', $cluster->id)
                ->where(function($query) {
                    $query->where('role', 'barangay')
                          ->orWhere('user_type', 'barangay');
                });

            // Apply search filter to barangays if specified
            if ($search) {
                $clusterBarangays->where('name', 'like', "%{$search}%");
            }

            $clusterBarangayIds = $clusterBarangays->pluck('id')->toArray();

            // Count submissions for all barangays in this cluster
            $submissionCount = 0;

            if (!empty($clusterBarangayIds)) {
                // Create queries for each report type
                $weeklyClusterQuery = WeeklyReport::whereIn('user_id', $clusterBarangayIds)
                    ->where('status', 'submitted');

                $monthlyClusterQuery = MonthlyReport::whereIn('user_id', $clusterBarangayIds)
                    ->where('status', 'submitted');

                $quarterlyClusterQuery = QuarterlyReport::whereIn('user_id', $clusterBarangayIds)
                    ->where('status', 'submitted');

                $semestralClusterQuery = SemestralReport::whereIn('user_id', $clusterBarangayIds)
                    ->where('status', 'submitted');

                $annualClusterQuery = AnnualReport::whereIn('user_id', $clusterBarangayIds)
                    ->where('status', 'submitted');

                // Apply date range filter if specified
                if ($startDate && $endDate) {
                    $weeklyClusterQuery->whereBetween('created_at', [$startDate, $endDate]);
                    $monthlyClusterQuery->whereBetween('created_at', [$startDate, $endDate]);
                    $quarterlyClusterQuery->whereBetween('created_at', [$startDate, $endDate]);
                    $semestralClusterQuery->whereBetween('created_at', [$startDate, $endDate]);
                    $annualClusterQuery->whereBetween('created_at', [$startDate, $endDate]);
                }

                // Apply search filter for report types if specified
                if ($search) {
                    $reportTypeIds = ReportType::where('name', 'like', "%{$search}%")
                        ->pluck('id')
                        ->toArray();

                    if (!empty($reportTypeIds)) {
                        $weeklyClusterQuery->whereIn('report_type_id', $reportTypeIds);
                        $monthlyClusterQuery->whereIn('report_type_id', $reportTypeIds);
                        $quarterlyClusterQuery->whereIn('report_type_id', $reportTypeIds);
                        $semestralClusterQuery->whereIn('report_type_id', $reportTypeIds);
                        $annualClusterQuery->whereIn('report_type_id', $reportTypeIds);
                    }
                }

                // Apply report type filter if specified
                if ($reportType) {
                    // Only count the specified report type
                    if ($reportType != 'weekly') $weeklyClusterQuery->whereRaw('1=0');
                    if ($reportType != 'monthly') $monthlyClusterQuery->whereRaw('1=0');
                    if ($reportType != 'quarterly') $quarterlyClusterQuery->whereRaw('1=0');
                    if ($reportType != 'semestral') $semestralClusterQuery->whereRaw('1=0');
                    if ($reportType != 'annual') $annualClusterQuery->whereRaw('1=0');
                }

                $submissionCount =
                    $weeklyClusterQuery->count() +
                    $monthlyClusterQuery->count() +
                    $quarterlyClusterQuery->count() +
                    $semestralClusterQuery->count() +
                    $annualClusterQuery->count();
            }

            $clusterSubmissions["Cluster " . $cluster->id] = $submissionCount;
        }

        // Get all clusters for the filter dropdown
        $allClusters = Cluster::all();

        return view('admin.dashboard', compact(
            'totalReportTypes',
            'totalSubmittedReports',
            'noSubmissionReports',
            'lateSubmissions',
            'weeklyCount',
            'monthlyCount',
            'quarterlyCount',
            'semestralCount',
            'annualCount',
            'submissionsByMonth',
            'topBarangays',
            'clusterSubmissions',
            'allClusters',
            'startDate',
            'endDate',
            'reportType',
            'clusterId',
            'search'
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
    public function userManagement(Request $request)
    {
        // Get the number of users per page from the request or use default
        $perPage = $request->get('per_page', 10);

        // Get users with their relationships for client-side filtering
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
            ->paginate($perPage)
            ->withQueryString(); // Preserve other query parameters

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
