@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Hidden form for filter state management -->
    <form id="dashboardFilterForm" action="{{ route('admin.dashboard') }}" method="GET" style="display: none;">
        <input type="hidden" id="report_type" name="report_type" value="{{ request('report_type') }}">
        <input type="hidden" id="cluster_id" name="cluster_id" value="{{ request('cluster_id') }}">
    </form>

    <!-- Stats Row -->
    <div class="row mb-4">
        <!-- Total Report Types Card -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="stat-icon primary-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $totalReportTypes }}</h3>
                        <p class="stat-label">Total Report Types</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Submitted Reports Card -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="stat-icon success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $totalSubmittedReports }}</h3>
                        <p class="stat-label">Total Submitted Reports</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Submission Reports Card -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="stat-icon warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $noSubmissionReports }}</h3>
                        <p class="stat-label">No Submissions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Submissions Card -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="stat-icon danger-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $lateSubmissions }}</h3>
                        <p class="stat-label">Late Submissions</p>
                    </div>
                </div>
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

                            // Get all barangay users
                            $barangayQuery = App\Models\User::where('user_type', 'barangay');

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

                                // We no longer use date filters

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

            <!-- Submissions per Cluster Cards -->
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
                    <div class="row cluster-cards">
                        @php
                            // Get all clusters
                            $allClusters = App\Models\Cluster::all();

                            // Get the current filter parameters
                            $reportType = request('report_type');

                            // Calculate submission counts for each cluster directly
                            $directClusterSubmissions = [];

                            foreach ($allClusters as $cluster) {
                                // Get all barangays in this cluster
                                $clusterBarangays = App\Models\User::where('cluster_id', $cluster->id)
                                    ->where('user_type', 'barangay');

                                $clusterBarangayIds = $clusterBarangays->pluck('id')->toArray();
                                $submissionCount = 0;

                                if (!empty($clusterBarangayIds)) {
                                    // Create queries for each report type with DISTINCT to prevent counting resubmissions
                                    $weeklyQuery = App\Models\WeeklyReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted')
                                        ->distinct('user_id', 'report_type_id');

                                    $monthlyQuery = App\Models\MonthlyReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted')
                                        ->distinct('user_id', 'report_type_id');

                                    $quarterlyQuery = App\Models\QuarterlyReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted')
                                        ->distinct('user_id', 'report_type_id');

                                    $semestralQuery = App\Models\SemestralReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted')
                                        ->distinct('user_id', 'report_type_id');

                                    $annualQuery = App\Models\AnnualReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted')
                                        ->distinct('user_id', 'report_type_id');

                                    $executiveOrderQuery = App\Models\ExecutiveOrder::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted')
                                        ->distinct('user_id', 'report_type_id');

                                    // We no longer use date filters

                                    // Apply report type filter if specified
                                    if ($reportType) {
                                        if ($reportType != 'weekly') $weeklyQuery->whereRaw('1=0');
                                        if ($reportType != 'monthly') $monthlyQuery->whereRaw('1=0');
                                        if ($reportType != 'quarterly') $quarterlyQuery->whereRaw('1=0');
                                        if ($reportType != 'semestral') $semestralQuery->whereRaw('1=0');
                                        if ($reportType != 'annual') $annualQuery->whereRaw('1=0');
                                        if ($reportType != 'executive_order') $executiveOrderQuery->whereRaw('1=0');
                                    }

                                    // Execute the queries and get the counts
                                    $weeklyCount = $weeklyQuery->count();
                                    $monthlyCount = $monthlyQuery->count();
                                    $quarterlyCount = $quarterlyQuery->count();
                                    $semestralCount = $semestralQuery->count();
                                    $annualCount = $annualQuery->count();
                                    $executiveOrderCount = $executiveOrderQuery->count();

                                    // Sum up the counts
                                    $submissionCount =
                                        $weeklyCount +
                                        $monthlyCount +
                                        $quarterlyCount +
                                        $semestralCount +
                                        $annualCount +
                                        $executiveOrderCount;
                                }

                                $directClusterSubmissions["Cluster " . $cluster->id] = $submissionCount;
                            }
                        @endphp

                        @foreach($directClusterSubmissions as $clusterName => $submissionCount)
                        <div class="col-6 col-md-6 mb-3">
                            <div class="cluster-card cluster-{{ substr($clusterName, -1) }}-card {{ request('cluster_id') == substr($clusterName, -1) ? 'active' : '' }}"
                                 data-cluster-id="{{ substr($clusterName, -1) }}">
                                <div class="cluster-icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div class="cluster-content">
                                    <h3 class="cluster-value">{{ $submissionCount }}</h3>
                                    <p class="cluster-label">{{ $clusterName }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Charts -->
        <div class="col-md-6">
            <!-- Monthly Trend Chart -->
            <div class="chart-card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-line"></i>
                        Monthly Submission Trend
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modern Admin Dashboard Styles */
    .container-fluid {
        padding: 2rem;
        background: transparent;
    }

    /* Modern Chart Container */
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 1rem;
        padding: 1rem;
    }

    /* Modern Stat Cards */
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    }

    .chart-card .card-header h5 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
    }

    .chart-card .card-header h5 i {
        margin-right: 0.5rem;
        color: #6366f1;
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

    // Initialize charts
    initCharts();

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

            // Store the current cluster card values before updating
            const currentClusterValues = {};
            document.querySelectorAll('.cluster-card').forEach(c => {
                const cId = c.getAttribute('data-cluster-id');
                const valueElement = c.querySelector('.cluster-value');
                if (valueElement) {
                    currentClusterValues[cId] = valueElement.textContent;
                }
            });
            console.log('Current cluster values before update:', currentClusterValues);

            // Show loading indicator
            document.body.style.cursor = 'wait';

            // Get other filter values
            const reportType = reportTypeSelect.value;

            // Make AJAX request to get updated chart data
            fetch(`{{ route('admin.dashboard.chart-data') }}?cluster_id=${clusterId}&report_type=${reportType}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);

                    if (data.success) {
                        // We need to update all cards dynamically
                        // But we'll make sure to preserve the values of non-filtered cards
                        if (data.data.clusterSubmissions) {
                            // We'll use the data from the server for all cards
                            // This ensures all cards are updated with the latest data
                            console.log('Updating all cluster cards with server data');
                        }

                        updateDashboard(data.data);
                    } else {
                        console.error('Error in response:', data.message || 'Unknown error');
                        alert('Error updating dashboard. Please try again.');
                    }

                    // Hide loading indicator
                    document.body.style.cursor = 'default';
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                    alert('Error updating dashboard. Please try again.');
                    // Hide loading indicator
                    document.body.style.cursor = 'default';
                });
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

        // Store the current cluster card values before updating
        const currentClusterValues = {};
        document.querySelectorAll('.cluster-card').forEach(c => {
            const cId = c.getAttribute('data-cluster-id');
            const valueElement = c.querySelector('.cluster-value');
            if (valueElement) {
                currentClusterValues[cId] = valueElement.textContent;
            }
        });
        console.log('Current cluster values before clearing filter:', currentClusterValues);

        // Show loading indicator
        document.body.style.cursor = 'wait';

        // Get other filter values
        const reportType = reportTypeSelect.value;

        // Make AJAX request to get updated chart data
        fetch(`{{ route('admin.dashboard.chart-data') }}?report_type=${reportType}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.success) {
                    // When clearing the filter, we want to update all cluster values
                    // with the new unfiltered data, so we don't need to preserve the old values
                    updateDashboard(data.data);
                } else {
                    console.error('Error in response:', data.message || 'Unknown error');
                    alert('Error updating dashboard. Please try again.');
                }

                // Hide loading indicator
                document.body.style.cursor = 'default';
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                alert('Error updating dashboard. Please try again.');
                // Hide loading indicator
                document.body.style.cursor = 'default';
            });
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

            // Show loading indicator
            document.body.style.cursor = 'wait';

            // Get other filter values
            const clusterId = clusterSelect.value;

            // Make AJAX request to get updated chart data
            fetch(`{{ route('admin.dashboard.chart-data') }}?report_type=${reportType}&cluster_id=${clusterId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);

                    if (data.success) {
                        // We need to update all cards dynamically
                        // This ensures all cards are updated with the latest data
                        console.log('Updating all report type cards with server data');

                        updateDashboard(data.data);
                    } else {
                        console.error('Error in response:', data.message || 'Unknown error');
                        alert('Error updating dashboard. Please try again.');
                    }

                    // Hide loading indicator
                    document.body.style.cursor = 'default';
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                    alert('Error updating dashboard. Please try again.');
                    // Hide loading indicator
                    document.body.style.cursor = 'default';
                });
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

        // Show loading indicator
        document.body.style.cursor = 'wait';

        // Get other filter values
        const clusterId = clusterSelect.value;

        // Make AJAX request to get updated chart data
        fetch(`{{ route('admin.dashboard.chart-data') }}?cluster_id=${clusterId}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.success) {
                    updateDashboard(data.data);
                } else {
                    console.error('Error in response:', data.message || 'Unknown error');
                    alert('Error updating dashboard. Please try again.');
                }

                // Hide loading indicator
                document.body.style.cursor = 'default';
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                alert('Error updating dashboard. Please try again.');
                // Hide loading indicator
                document.body.style.cursor = 'default';
            });
    });

    // Function to update dashboard with new data
    function updateDashboard(data) {
        console.log('Updating dashboard with data:', data);

        try {
            // Update stat cards
            const statCards = document.querySelectorAll('.stat-card');
            if (statCards.length >= 4) {
                statCards[0].querySelector('.stat-value').textContent = data.totalReportTypes;
                statCards[1].querySelector('.stat-value').textContent = data.totalSubmittedReports;
                statCards[2].querySelector('.stat-value').textContent = data.noSubmissionReports;
                statCards[3].querySelector('.stat-value').textContent = data.lateSubmissions;
            } else {
                console.error('Could not find all stat cards');
            }

            // Update report type distribution cards
            // Note: We're using the preserved values passed from the click handler
            // or the original values from the AJAX response
            const weeklyCard = document.querySelector('.weekly-card .report-type-value');
            const monthlyCard = document.querySelector('.monthly-card .report-type-value');
            const quarterlyCard = document.querySelector('.quarterly-card .report-type-value');
            const semestralCard = document.querySelector('.semestral-card .report-type-value');
            const annualCard = document.querySelector('.annual-card .report-type-value');

            // Only update if the data contains the values
            if (weeklyCard && data.weeklyCount !== undefined) weeklyCard.textContent = data.weeklyCount;
            if (monthlyCard && data.monthlyCount !== undefined) monthlyCard.textContent = data.monthlyCount;
            if (quarterlyCard && data.quarterlyCount !== undefined) quarterlyCard.textContent = data.quarterlyCount;
            if (semestralCard && data.semestralCount !== undefined) semestralCard.textContent = data.semestralCount;
            if (annualCard && data.annualCount !== undefined) annualCard.textContent = data.annualCount;

            // Update cluster cards values
            if (data.clusterSubmissions) {
                console.log('Updating cluster cards with values:', data.clusterSubmissions);

                // Get all cluster cards
                document.querySelectorAll('.cluster-card').forEach(card => {
                    const clusterId = card.getAttribute('data-cluster-id');
                    const clusterName = `Cluster ${clusterId}`;
                    const valueElement = card.querySelector('.cluster-value');

                    if (valueElement && data.clusterSubmissions[clusterName] !== undefined) {
                        valueElement.textContent = data.clusterSubmissions[clusterName];
                    }
                });
            } else {
                console.error('No cluster submissions data');
            }

            // Update charts
            updateCharts(data);
        } catch (error) {
            console.error('Error updating dashboard:', error);
        }
    }

    // Function to update charts
    function updateCharts(data) {
        console.log('Updating charts with data:', data);

        try {
            // Check if Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded!');
                return;
            }

            // Check if canvas elements exist
            const submissionTypeCtx = document.getElementById('submissionTypeChart');
            const monthlyTrendCtx = document.getElementById('monthlyTrendChart');

            if (!submissionTypeCtx) {
                console.error('Could not find submissionTypeChart canvas element');
            }

            if (!monthlyTrendCtx) {
                console.error('Could not find monthlyTrendChart canvas element');
            }

            // Destroy existing charts
            if (window.submissionTypeChart) {
                try {
                    window.submissionTypeChart.destroy();
                    console.log('Destroyed existing submission type chart');
                } catch (error) {
                    console.error('Error destroying submission type chart:', error);
                }
            }

            if (window.monthlyTrendChart) {
                try {
                    window.monthlyTrendChart.destroy();
                    console.log('Destroyed existing monthly trend chart');
                } catch (error) {
                    console.error('Error destroying monthly trend chart:', error);
                }
            }



            // Create monthly trend chart
            if (monthlyTrendCtx) {
                console.log('Creating new monthly trend chart with data:', data.submissionsByMonth);

                window.monthlyTrendChart = new Chart(monthlyTrendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Submissions',
                            data: data.submissionsByMonth,
                            borderColor: colors.primary,
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: colors.primary,
                            pointBorderWidth: 1.5,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            title: {
                                display: true,
                                text: 'Monthly Submission Trend',
                                font: {
                                    size: 14,
                                    weight: 'normal'
                                },
                                padding: {
                                    bottom: 15
                                },
                                color: '#495057'
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Submissions',
                                    font: {
                                        size: 12
                                    },
                                    padding: {
                                        top: 10
                                    },
                                    color: '#6c757d'
                                },
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.03)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month',
                                    font: {
                                        size: 12
                                    },
                                    padding: {
                                        top: 10
                                    },
                                    color: '#6c757d'
                                },
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });

                console.log('Monthly trend chart created successfully');
            }
        } catch (error) {
            console.error('Error updating charts:', error);
        }
    }

    // Function to initialize charts
    function initCharts() {
        console.log('Initializing charts');

        // Get initial data from the page
        const initialData = {
            weeklyCount: parseInt("{{ $weeklyCount }}"),
            monthlyCount: parseInt("{{ $monthlyCount }}"),
            quarterlyCount: parseInt("{{ $quarterlyCount }}"),
            semestralCount: parseInt("{{ $semestralCount }}"),
            annualCount: parseInt("{{ $annualCount }}"),
            executiveOrderCount: parseInt("{{ $executiveOrderCount ?? 0 }}"),
            submissionsByMonth: JSON.parse('{!! json_encode($submissionsByMonth) !!}'),
            topBarangays: JSON.parse('{!! json_encode($topBarangays) !!}'),
            barangayReportTypes: JSON.parse('{!! json_encode($barangayReportTypes) !!}')
        };





        // Create monthly trend chart
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
        if (monthlyTrendCtx) {
            window.monthlyTrendChart = new Chart(monthlyTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Submissions',
                        data: initialData.submissionsByMonth,
                        borderColor: colors.primary,
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: colors.primary,
                        pointBorderWidth: 1.5,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: true,
                            text: 'Monthly Submission Trend',
                            font: {
                                size: 14,
                                weight: 'normal'
                            },
                            padding: {
                                bottom: 15
                            },
                            color: '#495057'
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Submissions',
                                font: {
                                    size: 12
                                },
                                padding: {
                                    top: 10
                                },
                                color: '#6c757d'
                            },
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month',
                                font: {
                                    size: 12
                                },
                                padding: {
                                    top: 10
                                },
                                color: '#6c757d'
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
