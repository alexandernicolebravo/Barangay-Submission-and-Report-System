@extends('facilitator.layouts.app')

@section('title', 'Facilitator Dashboard')

@push('styles')
<style>
    /* Stats card styles */
    .stat-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Clickable stat card styles */
    .clickable-stat-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .clickable-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .clickable-stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        border: 2px solid #4361ee;
    }

    .clickable-stat-card.active .stat-icon {
        transform: scale(1.1);
    }

    .clickable-stat-card.active .stat-value {
        color: #4361ee;
        font-weight: 700;
    }

    .stat-card .card-body {
        display: flex;
        align-items: center;
        padding: 20px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .stat-icon i {
        font-size: 24px;
    }

    .primary-icon {
        background-color: rgba(67, 97, 238, 0.2);
    }

    .primary-icon i {
        color: #4361ee;
    }

    .success-icon {
        background-color: rgba(54, 179, 126, 0.2);
    }

    .success-icon i {
        color: #36b37e;
    }

    .warning-icon {
        background-color: rgba(255, 171, 0, 0.2);
    }

    .warning-icon i {
        color: #ffab00;
    }

    .danger-icon {
        background-color: rgba(245, 54, 92, 0.2);
    }

    .danger-icon i {
        color: #f5365c;
    }

    .stat-content {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 600;
        margin: 0;
        color: #2d3748;
    }

    .stat-label {
        font-size: 14px;
        color: #718096;
        margin: 0;
    }

    /* Chart card styles */
    .chart-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: none;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .chart-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .chart-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0;
    }

    .chart-card .card-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #4361ee;
    }

    .chart-card .card-body {
        padding: 20px;
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

    /* Cluster card styles */
    .cluster-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 15px;
        display: flex;
        align-items: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .cluster-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .cluster-card.active {
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }

    /* Active state for each cluster card */
    .cluster-1-card.active {
        background-color: rgba(67, 97, 238, 0.15);
    }

    .cluster-2-card.active {
        background-color: rgba(54, 179, 126, 0.15);
    }

    .cluster-3-card.active {
        background-color: rgba(255, 171, 0, 0.15);
    }

    .cluster-4-card.active {
        background-color: rgba(0, 184, 217, 0.15);
    }

    .cluster-icon {
        width: 45px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .cluster-1-card .cluster-icon {
        background-color: #4361ee;
    }

    .cluster-2-card .cluster-icon {
        background-color: #36b37e;
    }

    .cluster-3-card .cluster-icon {
        background-color: #ffab00;
    }

    .cluster-4-card .cluster-icon {
        background-color: #00b8d9;
    }

    .cluster-icon i {
        font-size: 20px;
        color: #fff;
    }

    .cluster-content {
        flex-grow: 1;
    }

    .cluster-value {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        color: #2d3748;
    }

    .cluster-label {
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

                            if (!empty($barangayIds)) {
                                // Weekly reports
                                $weeklyQuery = App\Models\WeeklyReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted');

                                // Monthly reports
                                $monthlyQuery = App\Models\MonthlyReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted');

                                // Quarterly reports
                                $quarterlyQuery = App\Models\QuarterlyReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted');

                                // Semestral reports
                                $semestralQuery = App\Models\SemestralReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted');

                                // Annual reports
                                $annualQuery = App\Models\AnnualReport::whereIn('user_id', $barangayIds)
                                    ->where('status', 'submitted');

                                // Execute the queries and get the counts
                                $directWeeklyCount = $weeklyQuery->count();
                                $directMonthlyCount = $monthlyQuery->count();
                                $directQuarterlyCount = $quarterlyQuery->count();
                                $directSemestralCount = $semestralQuery->count();
                                $directAnnualCount = $annualQuery->count();
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
                    </div>
                </div>
            </div>

            <!-- Cluster Submissions -->
            <div class="chart-card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-layer-group"></i>
                        Submissions per Cluster
                        <button id="clear-filter" class="btn btn-sm btn-outline-secondary float-end {{ request('cluster_id') ? '' : 'd-none' }}">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row cluster-cards" id="cluster-cards-container">
                        @php
                            // Get facilitator's assigned clusters
                            $facilitator = Auth::user();
                            $assignedClusters = [];

                            if (method_exists($facilitator, 'assignedClusters') && is_callable([$facilitator, 'assignedClusters'])) {
                                $assignedClusters = $facilitator->assignedClusters()->get();
                            }
                        @endphp

                        @foreach($assignedClusters as $index => $cluster)
                            @php
                                $clusterClass = 'cluster-' . (($index % 4) + 1) . '-card';
                            @endphp
                            <div class="col-6 col-md-6 mb-3">
                                <div class="cluster-card {{ $clusterClass }} {{ request('cluster_id') == $cluster->id ? 'active' : '' }}"
                                     data-cluster-id="{{ $cluster->id }}">
                                    <div class="cluster-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div class="cluster-content">
                                        <h3 class="cluster-value" data-cluster-id="{{ $cluster->id }}">{{ $clusterSubmissions[$cluster->id] ?? 0 }}</h3>
                                        <p class="cluster-label">{{ $cluster->name }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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

            <!-- Barangay Submissions Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-pie"></i>
                        Barangay Submissions
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="barangaySubmissionsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-clock"></i>
                        Recent Submissions
                        <a href="{{ route('facilitator.view-submissions') }}" class="btn btn-sm btn-primary float-end">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Report</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReports as $report)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2" style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                                @php
                                                    $extension = strtolower(pathinfo($report->file_path ?? '', PATHINFO_EXTENSION));
                                                    $icon = match($extension) {
                                                        'pdf' => 'fa-file-pdf',
                                                        'doc', 'docx' => 'fa-file-word',
                                                        'xls', 'xlsx' => 'fa-file-excel',
                                                        'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                                                        'txt' => 'fa-file-alt',
                                                        default => 'fa-file'
                                                    };
                                                @endphp
                                                <i class="fas {{ $icon }} fa-sm"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: var(--dark);">{{ $report->report_name }}</div>
                                                <small class="text-muted">{{ ucfirst($report->type) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $report->barangay_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $statusIcon = match($report->status) {
                                                'submitted' => 'fa-check-circle',
                                                'no submission' => 'fa-times-circle',
                                                'pending' => 'fa-clock',
                                                'approved' => 'fa-thumbs-up',
                                                'rejected' => 'fa-thumbs-down',
                                                default => 'fa-info-circle'
                                            };
                                            $statusClass = str_replace(' ', '-', $report->status);
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            <i class="fas {{ $statusIcon }}"></i>
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border: none;">
                                            <i class="fas fa-eye me-1"></i>
                                            View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">No recent submissions found.</p>
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
    const clusterSelect = document.getElementById('cluster_id');
    const filterForm = document.getElementById('dashboardFilterForm');
    const clearFilterBtn = document.getElementById('clear-filter');
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
    let barangaySubmissionsChart = null;

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
        clearFilterBtn.classList.add('d-none');
        clearReportTypeFilterBtn.classList.add('d-none');

        // Remove active class from all cards
        document.querySelectorAll('.clickable-stat-card, .cluster-card, .report-type-card').forEach(card => {
            card.classList.remove('active');
        });

        // Reset all filter values in the form
        document.getElementById('status').value = '';
        document.getElementById('timeliness').value = '';
        reportTypeSelect.value = '';
        clusterSelect.value = '';

        // Update the URL without reloading the page
        const url = new URL(window.location);
        url.searchParams.delete('status');
        url.searchParams.delete('timeliness');
        url.searchParams.delete('report_type');
        url.searchParams.delete('cluster_id');
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
        const hasActiveFilters = document.querySelector('.clickable-stat-card.active, .cluster-card.active, .report-type-card.active');
        if (!hasActiveFilters) {
            clearAllFiltersBtn.classList.add('d-none');
        }

        // Reload chart data and page content
        loadChartData();
        window.location.reload();
    }

    // Add click event to cluster cards
    document.querySelectorAll('.cluster-card').forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();

            const clusterId = this.getAttribute('data-cluster-id');
            console.log('Cluster card clicked:', clusterId);

            // If the card is already active, do nothing
            if (this.classList.contains('active')) {
                console.log('Card already active, doing nothing');
                return;
            }

            // Show the clear filter button
            clearFilterBtn.classList.remove('d-none');

            // Set active class on the selected cluster card
            document.querySelectorAll('.cluster-card').forEach(c => {
                c.classList.remove('active');
            });
            this.classList.add('active');

            // Set the cluster_id value in the form
            clusterSelect.value = clusterId;

            // Update the URL without reloading the page
            const url = new URL(window.location);
            url.searchParams.set('cluster_id', clusterId);
            window.history.pushState({}, '', url);

            // Reload chart data and page content
            loadChartData();
            window.location.reload();
        });
    });

    // Clear cluster filter button
    clearFilterBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Clear cluster filter button clicked');

        // Hide the clear filter button
        clearFilterBtn.classList.add('d-none');

        // Remove active class from all cluster cards
        document.querySelectorAll('.cluster-card').forEach(card => {
            card.classList.remove('active');
        });

        // Reset the cluster_id value in the form
        clusterSelect.value = '';

        // Update the URL without reloading the page
        const url = new URL(window.location);
        url.searchParams.delete('cluster_id');
        window.history.pushState({}, '', url);

        // Reload chart data and page content
        loadChartData();
        window.location.reload();
    });

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
        const clusterId = clusterSelect.value;
        const status = document.getElementById('status').value;
        const timeliness = document.getElementById('timeliness').value;

        // Build query parameters
        const params = new URLSearchParams();
        if (reportType) params.append('report_type', reportType);
        if (clusterId) params.append('cluster_id', clusterId);
        if (status) params.append('status', status);
        if (timeliness) params.append('timeliness', timeliness);

        // Fetch chart data from backend
        fetch(`{{ route('facilitator.dashboard.chart-data') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                updateCharts(data);
                updateClusterCards(data.clusterSubmissions || {});
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                // Fallback to default data
                updateCharts({
                    submissionsByMonth: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    reportTypeData: [0, 0, 0, 0, 0]
                });
                updateClusterCards({});
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

        // Update Barangay Submissions Chart
        const barangaySubmissionsCtx = document.getElementById('barangaySubmissionsChart');
        if (barangaySubmissionsCtx) {
            if (barangaySubmissionsChart) {
                barangaySubmissionsChart.destroy();
            }
            barangaySubmissionsChart = new Chart(barangaySubmissionsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Weekly', 'Monthly', 'Quarterly', 'Semestral', 'Annual'],
                    datasets: [{
                        data: data.reportTypeData,
                        backgroundColor: [
                            colors.primary,
                            colors.success,
                            colors.warning,
                            colors.info,
                            colors.danger
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }
    }

    function updateClusterCards(clusterSubmissions) {
        // Update cluster card values
        document.querySelectorAll('.cluster-value').forEach(element => {
            const clusterId = element.getAttribute('data-cluster-id');
            if (clusterId && clusterSubmissions.hasOwnProperty(clusterId)) {
                element.textContent = clusterSubmissions[clusterId];
            }
        });
    }
});
</script>
@endpush

@endsection
