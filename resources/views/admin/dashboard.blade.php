@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <form id="dashboardFilterForm" action="{{ route('admin.dashboard') }}" method="GET" class="mb-0">
                <div class="input-group">
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search barangay..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <!-- Hidden inputs to maintain filter state -->
                <input type="hidden" id="report_type" name="report_type" value="{{ request('report_type') }}">
                <input type="hidden" id="cluster_id" name="cluster_id" value="{{ request('cluster_id') }}">
            </form>
        </div>
    </div>

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
                            $search = request('search');

                            // Get all barangay users
                            $barangayQuery = App\Models\User::where('user_type', 'barangay');

                            // Apply cluster filter if specified
                            if ($clusterId) {
                                $barangayQuery->where('cluster_id', $clusterId);
                            }

                            // Apply search filter if specified
                            if ($search) {
                                $barangayQuery->where('name', 'like', "%{$search}%");
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

                                // We no longer use date filters

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
                            $search = request('search');

                            // Calculate submission counts for each cluster directly
                            $directClusterSubmissions = [];

                            foreach ($allClusters as $cluster) {
                                // Get all barangays in this cluster
                                $clusterBarangays = App\Models\User::where('cluster_id', $cluster->id)
                                    ->where('user_type', 'barangay');

                                // Apply search filter if specified
                                if ($search) {
                                    $clusterBarangays->where('name', 'like', "%{$search}%");
                                }

                                $clusterBarangayIds = $clusterBarangays->pluck('id')->toArray();
                                $submissionCount = 0;

                                if (!empty($clusterBarangayIds)) {
                                    // Create queries for each report type and execute them immediately
                                    $weeklyQuery = App\Models\WeeklyReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted');

                                    $monthlyQuery = App\Models\MonthlyReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted');

                                    $quarterlyQuery = App\Models\QuarterlyReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted');

                                    $semestralQuery = App\Models\SemestralReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted');

                                    $annualQuery = App\Models\AnnualReport::whereIn('user_id', $clusterBarangayIds)
                                        ->where('status', 'submitted');

                                    // We no longer use date filters

                                    // Apply report type filter if specified
                                    if ($reportType) {
                                        if ($reportType != 'weekly') $weeklyQuery->whereRaw('1=0');
                                        if ($reportType != 'monthly') $monthlyQuery->whereRaw('1=0');
                                        if ($reportType != 'quarterly') $quarterlyQuery->whereRaw('1=0');
                                        if ($reportType != 'semestral') $semestralQuery->whereRaw('1=0');
                                        if ($reportType != 'annual') $annualQuery->whereRaw('1=0');
                                    }

                                    // Execute the queries and get the counts
                                    $weeklyCount = $weeklyQuery->count();
                                    $monthlyCount = $monthlyQuery->count();
                                    $quarterlyCount = $quarterlyQuery->count();
                                    $semestralCount = $semestralQuery->count();
                                    $annualCount = $annualQuery->count();

                                    // Sum up the counts
                                    $submissionCount =
                                        $weeklyCount +
                                        $monthlyCount +
                                        $quarterlyCount +
                                        $semestralCount +
                                        $annualCount;
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

            <!-- Barangay Submissions Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-bar"></i>
                        Top 10 Barangay Submissions by Report Type
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="barangaySubmissionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Chart container styles */
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }

    /* Stat card styles */
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
        color: #fff;
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
        margin-bottom: 20px;
        overflow: hidden;
    }

    .chart-card .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 15px 20px;
    }

    .chart-card .card-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
    }

    .chart-card .card-header h5 i {
        margin-right: 8px;
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
    const searchInput = document.getElementById('search');
    const filterForm = document.getElementById('dashboardFilterForm');
    const clearFilterBtn = document.getElementById('clear-filter');
    const clearReportTypeFilterBtn = document.getElementById('clear-report-type-filter');

    // Add event listener for search input
    searchInput.addEventListener('input', function(e) {
        // Update the search parameter in the URL
        const url = new URL(window.location);
        if (this.value) {
            url.searchParams.set('search', this.value);
        } else {
            url.searchParams.delete('search');
        }
        window.history.pushState({}, '', url);
    });

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
            const search = searchInput.value;

            // Make AJAX request to get updated chart data
            fetch(`{{ route('admin.dashboard.chart-data') }}?cluster_id=${clusterId}&report_type=${reportType}&search=${search}`)
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
        const search = searchInput.value;

        // Make AJAX request to get updated chart data
        fetch(`{{ route('admin.dashboard.chart-data') }}?report_type=${reportType}&search=${search}`)
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
            const search = searchInput.value;

            // Make AJAX request to get updated chart data
            fetch(`{{ route('admin.dashboard.chart-data') }}?report_type=${reportType}&cluster_id=${clusterId}&search=${search}`)
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
        const search = searchInput.value;

        // Make AJAX request to get updated chart data
        fetch(`{{ route('admin.dashboard.chart-data') }}?cluster_id=${clusterId}&search=${search}`)
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

            // Create barangay submissions chart
            const barangaySubmissionsCtx = document.getElementById('barangaySubmissionsChart');
            if (barangaySubmissionsCtx) {
                // Get the top barangays data
                const barangayLabels = Object.keys(data.topBarangays);
                const barangayValues = Object.values(data.topBarangays);

                console.log('Creating new barangay submissions chart with data:', {
                    labels: barangayLabels,
                    values: barangayValues
                });

                // Destroy existing chart if it exists
                if (window.barangaySubmissionsChart) {
                    try {
                        window.barangaySubmissionsChart.destroy();
                        console.log('Destroyed existing barangay submissions chart');
                    } catch (error) {
                        console.error('Error destroying barangay submissions chart:', error);
                    }
                }

                // Get the total number of report types for max scale
                const totalReportTypes = data.totalReportTypes;

                // No need for dynamic height with only 10 barangays

                window.barangaySubmissionsChart = new Chart(barangaySubmissionsCtx, {
                    type: 'bar',
                    data: {
                        labels: barangayLabels,
                        datasets: [{
                            label: 'Submissions',
                            data: barangayValues,
                            backgroundColor: barangayLabels.map((_, index) => chartColors[index % chartColors.length]),
                            borderWidth: 0,
                            borderRadius: 4,
                            maxBarThickness: 25
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',  // Horizontal bar chart
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Submissions: ${context.raw}`;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'Top 10 Barangay Submissions',
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
                        scales: {
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            x: {
                                beginAtZero: true,
                                // Set max value to total report types
                                max: totalReportTypes,
                                title: {
                                    display: true,
                                    text: 'Number of Submissions (Max: Total Report Types)',
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
                                    },
                                    // Add step size to ensure we see reasonable tick marks
                                    stepSize: Math.max(1, Math.ceil(totalReportTypes / 10))
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.03)'
                                }
                            }
                        }
                    }
                });

                console.log('Barangay submissions chart created successfully');
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
            submissionsByMonth: JSON.parse('{!! json_encode($submissionsByMonth) !!}'),
            topBarangays: JSON.parse('{!! json_encode($topBarangays) !!}'),
            barangayReportTypes: JSON.parse('{!! json_encode($barangayReportTypes) !!}')
        };



        // Create barangay submissions chart
        const barangaySubmissionsCtx = document.getElementById('barangaySubmissionsChart');
        if (barangaySubmissionsCtx) {
            // Get the barangay data
            const barangayNames = Object.keys(initialData.topBarangays);
            const barangaySubmissions = Object.values(initialData.topBarangays);
            const barangayReportTypes = initialData.barangayReportTypes;

            console.log('Initializing barangay submissions chart with data:', {
                barangayNames,
                barangaySubmissions,
                barangayReportTypes
            });

            // Prepare datasets for stacked bar chart
            const datasets = [
                {
                    label: 'Weekly',
                    data: barangayNames.map(name => barangayReportTypes[name]?.weekly || 0),
                    backgroundColor: colors.primary,
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Monthly',
                    data: barangayNames.map(name => barangayReportTypes[name]?.monthly || 0),
                    backgroundColor: colors.success,
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Quarterly',
                    data: barangayNames.map(name => barangayReportTypes[name]?.quarterly || 0),
                    backgroundColor: colors.info,
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Semestral',
                    data: barangayNames.map(name => barangayReportTypes[name]?.semestral || 0),
                    backgroundColor: colors.warning,
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Annual',
                    data: barangayNames.map(name => barangayReportTypes[name]?.annual || 0),
                    backgroundColor: colors.danger,
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.8
                }
            ];

            window.barangaySubmissionsChart = new Chart(barangaySubmissionsCtx, {
                type: 'bar',
                data: {
                    labels: barangayNames,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',  // Horizontal bar chart
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 15,
                                padding: 10
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: true,
                            text: 'Top 10 Barangay Submissions by Report Type',
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
                    scales: {
                        x: {
                            stacked: true,
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
                        y: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

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
