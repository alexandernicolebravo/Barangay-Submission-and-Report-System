@extends('facilitator.layouts.app')

@section('title', 'Facilitator Dashboard')

@push('styles')
<style>
    /* Modern Facilitator Dashboard Styles */
    .container-fluid {
        padding: 2rem;
        background: transparent;
    }

    /* Modern Stats Cards */
    .stat-card {
        background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
        position: relative;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #c4b5fd;
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    /* Modern Clickable Stat Cards */
    .clickable-stat-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .clickable-stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.15), 0 15px 15px -5px rgba(0, 0, 0, 0.08);
    }

    .clickable-stat-card.active {
        transform: translateY(-4px);
        box-shadow: 0 25px 30px -5px rgba(139, 92, 246, 0.25), 0 15px 15px -5px rgba(139, 92, 246, 0.15);
        border: 2px solid #8b5cf6;
    }

    .clickable-stat-card.active::before {
        opacity: 1;
        height: 6px;
    }

    .clickable-stat-card.active .stat-icon {
        transform: scale(1.15);
    }

    .clickable-stat-card.active .stat-value {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    .stat-card .card-body {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        background: white;
    }

    /* Modern Stat Icons */
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.25rem;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        opacity: 0.1;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon::before {
        opacity: 0.2;
    }

    .stat-icon i {
        font-size: 1.5rem;
        color: white;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon i {
        transform: scale(1.1);
    }

    .primary-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .success-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .warning-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .danger-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .stat-content {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-value {
        transform: scale(1.05);
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
        font-weight: 500;
        margin-top: 0.25rem;
    }

    /* Modern Chart Cards */
    .chart-card {
        background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }

    .chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #c4b5fd;
    }

    .chart-card:hover::before {
        opacity: 1;
    }

    .chart-card .card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem;
        border-radius: 1rem 1rem 0 0;
    }

    .chart-card .card-header h5 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
    }

    .chart-card .card-header h5 i {
        margin-right: 0.5rem;
        color: #8b5cf6;
        font-size: 1rem;
    }

    .chart-card .card-body {
        padding: 1.5rem;
        background: white;
    }

    /* Report type card styles */
    .report-type-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 15px;
        display: flex;
        align-items: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .report-type-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .report-type-card.active {
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }

    /* Active state for each report type card */
    .weekly-card.active {
        background-color: rgba(67, 97, 238, 0.15);
    }

    .monthly-card.active {
        background-color: rgba(54, 179, 126, 0.15);
    }

    .quarterly-card.active {
        background-color: rgba(255, 171, 0, 0.15);
    }

    .semestral-card.active {
        background-color: rgba(0, 184, 217, 0.15);
    }

    .annual-card.active {
        background-color: rgba(245, 54, 92, 0.15);
    }

    .executive-order-card.active {
        background-color: rgba(111, 66, 193, 0.15);
    }

    .report-type-icon {
        width: 45px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .report-type-icon i {
        font-size: 20px;
        color: #fff;
    }

    .weekly-card .report-type-icon {
        background-color: #4361ee;
    }

    .monthly-card .report-type-icon {
        background-color: #36b37e;
    }

    .quarterly-card .report-type-icon {
        background-color: #ffab00;
    }

    .semestral-card .report-type-icon {
        background-color: #00b8d9;
    }

    .annual-card .report-type-icon {
        background-color: #f5365c;
    }

    .executive-order-card .report-type-icon {
        background-color: #6f42c1;
    }

    .report-type-content {
        flex-grow: 1;
    }

    .report-type-value {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        color: #2d3748;
    }

    .report-type-label {
        font-size: 12px;
        color: #718096;
        margin: 0;
    }


</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hidden form for filter state management -->
    <form id="dashboardFilterForm" action="{{ route('facilitator.dashboard') }}" method="GET" style="display: none;">
        <input type="hidden" id="report_type" name="report_type" value="{{ request('report_type') }}">
        <input type="hidden" id="cluster_id" name="cluster_id" value="{{ request('cluster_id') }}">
    </form>

    <!-- Stats Row -->
    <div class="row mb-4">
        <!-- Total Report Types Card -->
        <div class="col-md-3">
            <div class="stat-card clickable-stat-card" data-filter-type="all" data-filter-value="">
                <div class="card-body">
                    <div class="stat-icon primary-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $totalReportTypes ?? 0 }}</h3>
                        <p class="stat-label">Total Report Types</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Submitted Reports Card -->
        <div class="col-md-3">
            <div class="stat-card clickable-stat-card {{ request('status') == 'submitted' ? 'active' : '' }}" data-filter-type="status" data-filter-value="submitted">
                <div class="card-body">
                    <div class="stat-icon success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $totalSubmittedReports ?? 0 }}</h3>
                        <p class="stat-label">Total Submitted Reports</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Submission Reports Card -->
        <div class="col-md-3">
            <div class="stat-card clickable-stat-card {{ request('status') == 'no_submission' ? 'active' : '' }}" data-filter-type="status" data-filter-value="no_submission">
                <div class="card-body">
                    <div class="stat-icon warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $noSubmissionReports ?? 0 }}</h3>
                        <p class="stat-label">No Submissions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Submissions Card -->
        <div class="col-md-3">
            <div class="stat-card clickable-stat-card {{ request('timeliness') == 'late' ? 'active' : '' }}" data-filter-type="timeliness" data-filter-value="late">
                <div class="card-body">
                    <div class="stat-icon danger-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $lateSubmissions ?? 0 }}</h3>
                        <p class="stat-label">Late Submissions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Filters Row -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <button id="clear-all-filters" class="btn btn-outline-secondary btn-sm {{ request()->hasAny(['status', 'timeliness', 'report_type', 'cluster_id']) ? '' : 'd-none' }}">
                    <i class="fas fa-times me-1"></i> Clear All Filters
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Report Type Distribution and Cluster Submissions -->
        <div class="col-md-6">
            <!-- Report Type Distribution -->
            <div class="chart-card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-bar"></i>
                        Report Type Distribution
                        <button id="clear-report-type-filter" class="btn btn-sm btn-outline-secondary float-end {{ request('report_type') ? '' : 'd-none' }}">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row report-type-cards">
                        @php
                            // Get the current filter parameters
                            $clusterId = request('cluster_id');

                            // Get facilitator's assigned clusters
                            $facilitator = Auth::user();
                            $assignedClusterIds = [];
                            if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
                                $assignedClusterIds = $facilitator->assignedClusters()->pluck('clusters.id')->toArray();
                            }

                            // Get barangay users from facilitator's clusters
                            $barangayQuery = App\Models\User::where('user_type', 'barangay');

                            if (!empty($assignedClusterIds)) {
                                $barangayQuery->whereIn('cluster_id', $assignedClusterIds);
                            }

                            // Apply cluster filter if specified
                            if ($clusterId) {
                                $barangayQuery->where('cluster_id', $clusterId);
                            }

                            $barangayIds = $barangayQuery->pluck('id')->toArray();

                            // Calculate report counts directly
                            $directWeeklyCount = 0;
                            $directMonthlyCount = 0;
                            $directQuarterlyCount = 0;
                            $directSemestralCount = 0;
                            $directAnnualCount = 0;
                            $directExecutiveOrderCount = 0;

                            if (!empty($barangayIds)) {
                                // Weekly reports with DISTINCT to prevent counting resubmissions
                                $weeklyQuery = App\Models\WeeklyReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted')
                                    ->distinct('user_id', 'report_type_id');

                                // Monthly reports with DISTINCT to prevent counting resubmissions
                                $monthlyQuery = App\Models\MonthlyReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted')
                                    ->distinct('user_id', 'report_type_id');

                                // Quarterly reports with DISTINCT to prevent counting resubmissions
                                $quarterlyQuery = App\Models\QuarterlyReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted')
                                    ->distinct('user_id', 'report_type_id');

                                // Semestral reports with DISTINCT to prevent counting resubmissions
                                $semestralQuery = App\Models\SemestralReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted')
                                    ->distinct('user_id', 'report_type_id');

                                // Annual reports with DISTINCT to prevent counting resubmissions
                                $annualQuery = App\Models\AnnualReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted')
                                    ->distinct('user_id', 'report_type_id');

                                // Executive Order reports with DISTINCT to prevent counting resubmissions
                                $executiveOrderQuery = App\Models\ExecutiveOrder::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted')
                                    ->distinct('user_id', 'report_type_id');

                                // Execute the queries and get the counts
                                $directWeeklyCount = $weeklyQuery->count();
                                $directMonthlyCount = $monthlyQuery->count();
                                $directQuarterlyCount = $quarterlyQuery->count();
                                $directSemestralCount = $semestralQuery->count();
                                $directAnnualCount = $annualQuery->count();
                                $directExecutiveOrderCount = $executiveOrderQuery->count();
                            }
                        @endphp

                        <!-- Weekly Reports Card -->
                        <div class="col-6 col-md-4 mb-3">
                            <div class="report-type-card weekly-card {{ request('report_type') == 'weekly' ? 'active' : '' }}"
                                 data-report-type="weekly">
                                <div class="report-type-icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div class="report-type-content">
                                    <h3 class="report-type-value">{{ $directWeeklyCount }}</h3>
                                    <p class="report-type-label">Weekly Reports</p>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Reports Card -->
                        <div class="col-6 col-md-4 mb-3">
                            <div class="report-type-card monthly-card {{ request('report_type') == 'monthly' ? 'active' : '' }}"
                                 data-report-type="monthly">
                                <div class="report-type-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="report-type-content">
                                    <h3 class="report-type-value">{{ $directMonthlyCount }}</h3>
                                    <p class="report-type-label">Monthly Reports</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quarterly Reports Card -->
                        <div class="col-6 col-md-4 mb-3">
                            <div class="report-type-card quarterly-card {{ request('report_type') == 'quarterly' ? 'active' : '' }}"
                                 data-report-type="quarterly">
                                <div class="report-type-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="report-type-content">
                                    <h3 class="report-type-value">{{ $directQuarterlyCount }}</h3>
                                    <p class="report-type-label">Quarterly Reports</p>
                                </div>
                            </div>
                        </div>

                        <!-- Semestral Reports Card -->
                        <div class="col-6 col-md-4 mb-3">
                            <div class="report-type-card semestral-card {{ request('report_type') == 'semestral' ? 'active' : '' }}"
                                 data-report-type="semestral">
                                <div class="report-type-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="report-type-content">
                                    <h3 class="report-type-value">{{ $directSemestralCount }}</h3>
                                    <p class="report-type-label">Semestral Reports</p>
                                </div>
                            </div>
                        </div>

                        <!-- Annual Reports Card -->
                        <div class="col-6 col-md-4 mb-3">
                            <div class="report-type-card annual-card {{ request('report_type') == 'annual' ? 'active' : '' }}"
                                 data-report-type="annual">
                                <div class="report-type-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="report-type-content">
                                    <h3 class="report-type-value">{{ $directAnnualCount }}</h3>
                                    <p class="report-type-label">Annual Reports</p>
                                </div>
                            </div>
                        </div>

                        <!-- Executive Order Reports Card -->
                        <div class="col-6 col-md-4 mb-3">
                            <div class="report-type-card executive-order-card {{ request('report_type') == 'executive_order' ? 'active' : '' }}"
                                 data-report-type="executive_order">
                                <div class="report-type-icon">
                                    <i class="fas fa-gavel"></i>
                                </div>
                                <div class="report-type-content">
                                    <h3 class="report-type-value">{{ $directExecutiveOrderCount }}</h3>
                                    <p class="report-type-label">Executive Orders</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Right Column - Charts and Recent Submissions -->
        <div class="col-md-6">
            <!-- Monthly Trend Chart -->
            <div class="chart-card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-line"></i>
                        Monthly Trend
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Submission Categories Row - Full Width -->
    <div class="row mb-4">
                <!-- On Time Submissions -->
                <div class="col-md-4">
                    <div class="submission-card on-time-card">
                        <div class="submission-header">
                            <div class="submission-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="submission-title">
                                <h6 class="mb-0">On Time Submissions</h6>
                                <span class="submission-count">{{ count($onTimeBarangays ?? []) }} barangays</span>
                            </div>
                            @if(count($onTimeBarangays ?? []) > 5)
                                <button class="btn btn-sm btn-view-all" onclick="viewAllSubmissions('on-time')">
                                    <i class="fas fa-external-link-alt"></i>
                                </button>
                            @endif
                        </div>
                        <div class="submission-body">
                            <div class="submission-list">
                                @forelse(array_slice($onTimeBarangays ?? [], 0, 5) as $barangay)
                                <div class="submission-item">
                                    <div class="d-flex align-items-center">
                                        <div class="barangay-avatar success">
                                            {{ substr($barangay['name'], 0, 2) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="barangay-name">{{ $barangay['name'] }}</div>
                                            <div class="cluster-name">{{ $barangay['cluster_name'] }}</div>
                                        </div>
                                        <div class="submission-badge success">
                                            {{ $barangay['on_time_count'] }}
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="empty-state">
                                    <i class="fas fa-clock"></i>
                                    <p>No on-time submissions yet</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Late Submissions -->
                <div class="col-md-4">
                    <div class="submission-card late-card">
                        <div class="submission-header">
                            <div class="submission-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="submission-title">
                                <h6 class="mb-0">Late Submissions</h6>
                                <span class="submission-count">{{ count($lateBarangays ?? []) }} barangays</span>
                            </div>
                            @if(count($lateBarangays ?? []) > 5)
                                <button class="btn btn-sm btn-view-all" onclick="viewAllSubmissions('late')">
                                    <i class="fas fa-external-link-alt"></i>
                                </button>
                            @endif
                        </div>
                        <div class="submission-body">
                            <div class="submission-list">
                                @forelse(array_slice($lateBarangays ?? [], 0, 5) as $barangay)
                                <div class="submission-item">
                                    <div class="d-flex align-items-center">
                                        <div class="barangay-avatar warning">
                                            {{ substr($barangay['name'], 0, 2) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="barangay-name">{{ $barangay['name'] }}</div>
                                            <div class="cluster-name">{{ $barangay['cluster_name'] }}</div>
                                        </div>
                                        <div class="submission-badge warning">
                                            {{ $barangay['late_count'] }}
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="empty-state">
                                    <i class="fas fa-clock"></i>
                                    <p>No late submissions</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Submissions -->
                <div class="col-md-4">
                    <div class="submission-card no-submission-card">
                        <div class="submission-header">
                            <div class="submission-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="submission-title">
                                <h6 class="mb-0">No Submissions</h6>
                                <span class="submission-count">{{ count($noSubmissionBarangays ?? []) }} barangays</span>
                            </div>
                        </div>
                        <div class="submission-body">
                            <div class="submission-list" style="max-height: 400px; overflow-y: auto;">
                                @forelse($noSubmissionBarangays ?? [] as $barangay)
                                <div class="submission-item">
                                    <div class="d-flex align-items-center">
                                        <div class="barangay-avatar danger">
                                            {{ substr($barangay['name'], 0, 2) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="barangay-name">{{ $barangay['name'] }}</div>
                                            <div class="cluster-name">{{ $barangay['cluster_name'] }}</div>
                                        </div>
                                        <div class="submission-badge danger">
                                            {{ $barangay['pending_submissions'] ?? $barangay['no_submission'] ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="empty-state success">
                                    <i class="fas fa-check-circle"></i>
                                    <p>All barangays have submitted!</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Barangay Summary - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="fas fa-users"></i>
                        Barangay Summary
                    </h5>
                    <div class="filter-controls">
                        <select id="clusterFilter" class="form-select form-select-sm me-2" style="width: auto; display: inline-block;">
                            <option value="">All Clusters</option>
                            @foreach($assignedClusters ?? [] as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                        <select id="reportTypeFilter" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                            <option value="">All Report Types</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="semestral">Semestral</option>
                            <option value="annual">Annual</option>
                            <option value="executive_order">Executive Order</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="barangayTable">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th>Cluster</th>
                                    <th>Total Reports</th>
                                    <th>On Time</th>
                                    <th>Late</th>
                                    <th>No Submission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangaySummary ?? [] as $barangay)
                                <tr data-cluster="{{ $barangay['cluster_id'] }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="barangay-avatar me-2">
                                                {{ substr($barangay['name'], 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $barangay['name'] }}</div>
                                                <small class="text-muted">{{ $barangay['email'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="cluster-badge cluster-{{ $barangay['cluster_id'] }}">
                                            {{ $barangay['cluster_name'] }}
                                        </span>
                                    </td>
                                    <td>{{ $barangay['total_reports'] }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $barangay['on_time'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $barangay['late'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $barangay['pending_submissions'] ?? $barangay['no_submission'] ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewBarangayDetails({{ $barangay['id'] }})">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-primary"></i>
                        Recent Submissions
                    </h5>
                    <a href="{{ route('facilitator.view-submissions') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold">Report</th>
                                    <th class="border-0 fw-semibold">Barangay</th>
                                    <th class="border-0 fw-semibold">Date</th>
                                    <th class="border-0 fw-semibold">Status</th>
                                    <th class="border-0 fw-semibold text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReports as $report)
                                <tr class="border-bottom">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon me-3">
                                                @php
                                                    $extension = strtolower(pathinfo($report->file_path ?? '', PATHINFO_EXTENSION));
                                                    $iconData = match($extension) {
                                                        'pdf' => ['icon' => 'fa-file-pdf', 'color' => '#dc3545'],
                                                        'doc', 'docx' => ['icon' => 'fa-file-word', 'color' => '#0d6efd'],
                                                        'xls', 'xlsx' => ['icon' => 'fa-file-excel', 'color' => '#198754'],
                                                        'jpg', 'jpeg', 'png', 'gif' => ['icon' => 'fa-file-image', 'color' => '#fd7e14'],
                                                        'txt' => ['icon' => 'fa-file-alt', 'color' => '#6c757d'],
                                                        default => ['icon' => 'fa-file', 'color' => '#6f42c1']
                                                    };
                                                @endphp
                                                <i class="fas {{ $iconData['icon'] }}" style="color: {{ $iconData['color'] }}; font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $report->report_name }}</div>
                                                <small class="text-muted">{{ ucfirst($report->type) }} Report</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="barangay-avatar-sm me-2">
                                                {{ substr($report->barangay_name, 0, 2) }}
                                            </div>
                                            <span class="fw-medium">{{ $report->barangay_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="text-dark">{{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($report->created_at)->format('h:i A') }}</small>
                                    </td>
                                    <td class="py-3">
                                        @php
                                            $statusConfig = match($report->status) {
                                                'submitted' => ['icon' => 'fa-check-circle', 'class' => 'success', 'text' => 'Submitted'],
                                                'no submission' => ['icon' => 'fa-times-circle', 'class' => 'danger', 'text' => 'No Submission'],
                                                'pending' => ['icon' => 'fa-clock', 'class' => 'warning', 'text' => 'Pending'],
                                                'approved' => ['icon' => 'fa-thumbs-up', 'class' => 'info', 'text' => 'Approved'],
                                                'rejected' => ['icon' => 'fa-thumbs-down', 'class' => 'danger', 'text' => 'Rejected'],
                                                default => ['icon' => 'fa-info-circle', 'class' => 'secondary', 'text' => ucfirst($report->status)]
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusConfig['class'] }} d-inline-flex align-items-center">
                                            <i class="fas {{ $statusConfig['icon'] }} me-1"></i>
                                            {{ $statusConfig['text'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="View Report">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox text-muted mb-3" style="font-size: 3rem;"></i>
                                            <h6 class="text-muted">No recent submissions found</h6>
                                            <p class="text-muted small mb-0">Recent submissions will appear here once barangays start submitting reports.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --primary-rgb: 13, 110, 253;
        --secondary-rgb: 108, 117, 125;
        --info-rgb: 13, 202, 240;
        --primary-light: rgba(var(--primary-rgb), 0.1);
        --dark: #212529;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.375rem;
        text-decoration: none;
        border: 1px solid transparent;
    }

    .status-badge i {
        margin-right: 0.25rem;
        font-size: 0.7rem;
    }

    .status-badge.submitted {
        color: #0f5132;
        background-color: #d1e7dd;
        border-color: #badbcc;
    }

    .status-badge.pending {
        color: #664d03;
        background-color: #fff3cd;
        border-color: #ffecb5;
    }

    .status-badge.approved {
        color: #055160;
        background-color: #cff4fc;
        border-color: #b6effb;
    }

    .status-badge.rejected {
        color: #842029;
        background-color: #f8d7da;
        border-color: #f5c2c7;
    }

    .status-badge.no-submission {
        color: #495057;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .table th {
        background: var(--light);
        font-weight: 600;
    }

    /* Summary Cards */
    .summary-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .summary-card.on-time {
        border-left: 4px solid #198754;
    }

    .summary-card.late {
        border-left: 4px solid #ffc107;
    }

    .summary-card.no-submission {
        border-left: 4px solid #dc3545;
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .summary-card.on-time .summary-icon {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .summary-card.late .summary-icon {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .summary-card.no-submission .summary-icon {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: #212529;
    }

    .summary-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
        font-weight: 500;
    }

    /* Barangay Avatar */
    .barangay-avatar {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
    }

    .barangay-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    /* Cluster Badges */
    .cluster-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cluster-badge.cluster-1 {
        background: rgba(67, 97, 238, 0.1);
        color: #4361ee;
        border: 1px solid rgba(67, 97, 238, 0.2);
    }

    .cluster-badge.cluster-2 {
        background: rgba(54, 179, 126, 0.1);
        color: #36b37e;
        border: 1px solid rgba(54, 179, 126, 0.2);
    }

    .cluster-badge.cluster-3 {
        background: rgba(255, 171, 0, 0.1);
        color: #ffab00;
        border: 1px solid rgba(255, 171, 0, 0.2);
    }

    .cluster-badge.cluster-4 {
        background: rgba(0, 184, 217, 0.1);
        color: #00b8d9;
        border: 1px solid rgba(0, 184, 217, 0.2);
    }

    /* Enhanced Table Styles */
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
    }

    .file-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-state {
        padding: 2rem;
    }

    /* Filter Controls */
    .filter-controls {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .filter-controls .form-select {
        min-width: 150px;
    }

    /* Modern Submission Cards */
    .submission-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .submission-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .submission-header {
        padding: 1.5rem 1.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .submission-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .on-time-card .submission-icon {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .late-card .submission-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .no-submission-card .submission-icon {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .submission-title h6 {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
    }

    .submission-count {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
    }

    .btn-view-all {
        background: rgba(0, 0, 0, 0.05);
        border: none;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        transition: all 0.2s ease;
        margin-left: auto;
    }

    .btn-view-all:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #374151;
        transform: scale(1.05);
    }

    .submission-body {
        padding: 0;
    }

    .submission-list {
        max-height: 280px;
        overflow-y: auto;
    }

    .submission-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .submission-item:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .submission-item:last-child {
        border-bottom: none;
    }

    .barangay-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        flex-shrink: 0;
        margin-right: 0.75rem;
    }

    .barangay-avatar.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .barangay-avatar.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .barangay-avatar.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .barangay-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .cluster-name {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
        margin-top: 2px;
    }

    .submission-badge {
        min-width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
        color: white;
    }

    .submission-badge.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .submission-badge.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .submission-badge.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .empty-state {
        padding: 2rem 1.5rem;
        text-align: center;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .empty-state.success {
        color: #10b981;
    }

    .empty-state.success i {
        color: #10b981;
        opacity: 0.8;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM content loaded');

    // Chart colors
    const colors = {
        primary: 'rgba(67, 97, 238, 0.7)',
        success: 'rgba(54, 179, 126, 0.7)',
        warning: 'rgba(255, 171, 0, 0.7)',
        danger: 'rgba(245, 54, 92, 0.7)',
        info: 'rgba(0, 184, 217, 0.7)',
        purple: 'rgba(111, 66, 193, 0.7)',
        orange: 'rgba(253, 126, 20, 0.7)',
        teal: 'rgba(32, 201, 151, 0.7)'
    };

    const chartColors = [
        colors.primary,
        colors.success,
        colors.warning,
        colors.info,
        colors.danger,
        colors.purple,
        colors.orange,
        colors.teal
    ];

    // Form elements
    const reportTypeSelect = document.getElementById('report_type');
    const filterForm = document.getElementById('dashboardFilterForm');
    const clearReportTypeFilterBtn = document.getElementById('clear-report-type-filter');
    const clearAllFiltersBtn = document.getElementById('clear-all-filters');

    // Add hidden inputs for new filter types
    if (!document.getElementById('status')) {
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.id = 'status';
        statusInput.name = 'status';
        statusInput.value = '{{ request("status") }}';
        filterForm.appendChild(statusInput);
    }

    if (!document.getElementById('timeliness')) {
        const timelinessInput = document.createElement('input');
        timelinessInput.type = 'hidden';
        timelinessInput.id = 'timeliness';
        timelinessInput.name = 'timeliness';
        timelinessInput.value = '{{ request("timeliness") }}';
        filterForm.appendChild(timelinessInput);
    }

    // Initialize charts
    initCharts();

    // Store chart instances globally for updates
    let monthlyTrendChart = null;

    // Add click event to stat cards
    document.querySelectorAll('.clickable-stat-card').forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();

            const filterType = this.getAttribute('data-filter-type');
            const filterValue = this.getAttribute('data-filter-value');

            console.log('Stat card clicked:', filterType, filterValue);

            // Handle different filter types
            if (filterType === 'all') {
                // Clear all filters
                clearAllFilters();
                return;
            }

            // If the card is already active, clear the filter
            if (this.classList.contains('active')) {
                console.log('Card already active, clearing filter');
                clearSpecificFilter(filterType);
                return;
            }

            // Show the clear all filters button
            clearAllFiltersBtn.classList.remove('d-none');

            // Set active class on the selected stat card
            document.querySelectorAll('.clickable-stat-card').forEach(c => {
                if (c.getAttribute('data-filter-type') === filterType) {
                    c.classList.remove('active');
                }
            });
            this.classList.add('active');

            // Set the filter value in the form
            if (filterType === 'status') {
                document.getElementById('status').value = filterValue;
            } else if (filterType === 'timeliness') {
                document.getElementById('timeliness').value = filterValue;
            }

            // Update the URL without reloading the page
            const url = new URL(window.location);
            url.searchParams.set(filterType, filterValue);
            window.history.pushState({}, '', url);

            // Reload chart data to reflect the filter
            loadChartData();
        });
    });

    // Clear all filters button
    clearAllFiltersBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Clear all filters button clicked');
        clearAllFilters();
    });

    function clearAllFilters() {
        // Hide the clear all filters button
        clearAllFiltersBtn.classList.add('d-none');
        clearReportTypeFilterBtn.classList.add('d-none');

        // Remove active class from all cards
        document.querySelectorAll('.clickable-stat-card, .report-type-card').forEach(card => {
            card.classList.remove('active');
        });

        // Reset all filter values in the form
        document.getElementById('status').value = '';
        document.getElementById('timeliness').value = '';
        reportTypeSelect.value = '';

        // Update the URL without reloading the page
        const url = new URL(window.location);
        url.searchParams.delete('status');
        url.searchParams.delete('timeliness');
        url.searchParams.delete('report_type');
        window.history.pushState({}, '', url);

        // Reload chart data and page content
        loadChartData();
        window.location.reload();
    }

    function clearSpecificFilter(filterType) {
        // Remove active class from the specific filter cards
        document.querySelectorAll(`.clickable-stat-card[data-filter-type="${filterType}"]`).forEach(card => {
            card.classList.remove('active');
        });

        // Reset the specific filter value in the form
        if (filterType === 'status') {
            document.getElementById('status').value = '';
        } else if (filterType === 'timeliness') {
            document.getElementById('timeliness').value = '';
        }

        // Update the URL without reloading the page
        const url = new URL(window.location);
        url.searchParams.delete(filterType);
        window.history.pushState({}, '', url);

        // Check if any filters are still active
        const hasActiveFilters = document.querySelector('.clickable-stat-card.active, .report-type-card.active');
        if (!hasActiveFilters) {
            clearAllFiltersBtn.classList.add('d-none');
        }

        // Reload chart data and page content
        loadChartData();
        window.location.reload();
    }



    // Add click event to report type cards
    document.querySelectorAll('.report-type-card').forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();

            const reportType = this.getAttribute('data-report-type');
            console.log('Report type card clicked:', reportType);

            // If the card is already active, do nothing
            if (this.classList.contains('active')) {
                console.log('Card already active, doing nothing');
                return;
            }

            // Show the clear filter button
            clearReportTypeFilterBtn.classList.remove('d-none');

            // Set active class on the selected report type card
            document.querySelectorAll('.report-type-card').forEach(c => {
                c.classList.remove('active');
            });
            this.classList.add('active');

            // Set the report_type value in the form
            reportTypeSelect.value = reportType;

            // Update the URL without reloading the page
            const url = new URL(window.location);
            url.searchParams.set('report_type', reportType);
            window.history.pushState({}, '', url);

            // Reload chart data and page content
            loadChartData();
            window.location.reload();
        });
    });

    // Clear report type filter button
    clearReportTypeFilterBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Clear report type filter button clicked');

        // Hide the clear filter button
        clearReportTypeFilterBtn.classList.add('d-none');

        // Remove active class from all report type cards
        document.querySelectorAll('.report-type-card').forEach(card => {
            card.classList.remove('active');
        });

        // Reset the report_type value in the form
        reportTypeSelect.value = '';

        // Update the URL without reloading the page
        const url = new URL(window.location);
        url.searchParams.delete('report_type');
        window.history.pushState({}, '', url);

        // Reload chart data and page content
        loadChartData();
        window.location.reload();
    });

    function initCharts() {
        // Load initial chart data
        loadChartData();
    }

    function loadChartData() {
        // Get current filter values
        const reportType = reportTypeSelect.value;
        const status = document.getElementById('status').value;
        const timeliness = document.getElementById('timeliness').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (reportType) params.append('report_type', reportType);
        if (status) params.append('status', status);
        if (timeliness) params.append('timeliness', timeliness);

        // Fetch chart data from backend
        fetch(`{{ route('facilitator.dashboard.chart-data') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                updateCharts(data);
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                // Fallback to default data
                updateCharts({
                    submissionsByMonth: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    reportTypeData: [0, 0, 0, 0, 0, 0]
                });
            });
    }

    function updateCharts(data) {
        // Update Monthly Trend Chart
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
        if (monthlyTrendCtx) {
            if (monthlyTrendChart) {
                monthlyTrendChart.destroy();
            }
            monthlyTrendChart = new Chart(monthlyTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Submissions',
                        data: data.submissionsByMonth,
                        borderColor: colors.primary,
                        backgroundColor: colors.primary.replace('0.7', '0.1'),
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

    }

    // Filter functionality for Barangay Summary table
    const clusterFilter = document.getElementById('clusterFilter');
    const reportTypeFilter = document.getElementById('reportTypeFilter');
    const barangayTable = document.getElementById('barangayTable');

    if (clusterFilter && reportTypeFilter && barangayTable) {
        function filterTable() {
            const clusterValue = clusterFilter.value;
            const reportTypeValue = reportTypeFilter.value;
            const rows = barangayTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const clusterData = row.getAttribute('data-cluster');
                let showRow = true;

                // Filter by cluster
                if (clusterValue && clusterData !== clusterValue) {
                    showRow = false;
                }

                // Show/hide row
                row.style.display = showRow ? '' : 'none';
            });
        }

        clusterFilter.addEventListener('change', filterTable);
        reportTypeFilter.addEventListener('change', filterTable);
    }

    // View Barangay Details function
    window.viewBarangayDetails = function(barangayId) {
        // Redirect to view submissions with barangay filter
        window.location.href = `{{ route('facilitator.view-submissions') }}?barangay_id=${barangayId}`;
    };

    // View All Submissions function
    window.viewAllSubmissions = function(type) {
        // Redirect to view submissions with status filter
        let statusFilter = '';
        switch(type) {
            case 'on-time':
                statusFilter = 'timeliness=on_time';
                break;
            case 'late':
                statusFilter = 'timeliness=late';
                break;
            case 'no-submission':
                statusFilter = 'status=no_submission';
                break;
        }
        window.location.href = `{{ route('facilitator.view-submissions') }}?${statusFilter}`;
    };
});
</script>
@endpush

@endsection
