@extends('admin.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Filter Bar Styles */
    .filter-bar {
        background-color: white;
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-shadow);
        transition: all var(--transition-speed) ease;
        padding: 15px;
        margin-bottom: 20px;
    }

    .filter-bar:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .filter-label {
        width: 20px;
        text-align: center;
    }

    .filter-bar .form-control,
    .filter-bar .form-select {
        font-size: 0.875rem;
        border-color: var(--gray-300);
        background-color: var(--gray-100);
    }

    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        box-shadow: none;
        border-color: var(--primary-color);
        background-color: white;
    }

    .date-separator {
        display: flex;
        align-items: center;
        color: var(--gray-600);
        font-weight: var(--font-weight-medium);
    }

    :root {
        /* Color Variables */
        --primary-color: #4361ee;
        --primary-light: rgba(67, 97, 238, 0.1);
        --success-color: #36b37e;
        --success-light: rgba(54, 179, 126, 0.1);
        --warning-color: #ffab00;
        --warning-light: rgba(255, 171, 0, 0.1);
        --danger-color: #f5365c;
        --danger-light: rgba(245, 54, 92, 0.1);
        --info-color: #00b8d9;
        --info-light: rgba(0, 184, 217, 0.1);
        --dark-color: #2d3748;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;

        /* Spacing & Sizing */
        --card-border-radius: 8px;
        --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        --card-hover-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
        --transition-speed: 0.2s;
        --section-spacing: 1.5rem;
        --card-padding: 1.25rem;

        /* Typography */
        --font-weight-normal: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;
        --font-size-xs: 0.75rem;
        --font-size-sm: 0.875rem;
        --font-size-md: 1rem;
        --font-size-lg: 1.25rem;
        --font-size-xl: 1.5rem;
        --font-size-xxl: 2rem;
        --line-height-tight: 1.2;
        --line-height-normal: 1.5;
    }

    /* Global Styles */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f9fafb;
        color: var(--gray-800);
    }

    /* Dashboard Container */
    .dashboard-container {
        padding: var(--section-spacing) 0;
    }

    /* Stat Card Styles */
    .stat-card {
        border-radius: var(--card-border-radius);
        border: none;
        box-shadow: var(--card-shadow);
        transition: all var(--transition-speed) ease;
        background: white;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .stat-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .stat-card .card-body {
        padding: var(--card-padding);
        display: flex;
        align-items: center;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--font-size-md);
        margin-right: 1rem;
    }

    .stat-icon.primary-icon {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .stat-icon.success-icon {
        background-color: var(--success-light);
        color: var(--success-color);
    }

    .stat-icon.warning-icon {
        background-color: var(--warning-light);
        color: var(--warning-color);
    }

    .stat-icon.danger-icon {
        background-color: var(--danger-light);
        color: var(--danger-color);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        margin: 0;
        color: var(--dark-color);
        line-height: var(--line-height-tight);
    }

    .stat-label {
        color: var(--gray-600);
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-medium);
        margin: 0.25rem 0 0;
    }

    /* Chart Card Styles */
    .chart-card {
        border-radius: var(--card-border-radius);
        border: none;
        box-shadow: var(--card-shadow);
        transition: all var(--transition-speed) ease;
        background: white;
        height: 100%;
        margin-bottom: 1.5rem;
    }

    .chart-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .chart-card .card-header {
        background: white;
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem var(--card-padding);
    }

    .chart-card .card-header h5 {
        font-weight: var(--font-weight-semibold);
        font-size: var(--font-size-md);
        color: var(--dark-color);
        margin: 0;
        display: flex;
        align-items: center;
    }

    .chart-card .card-header i {
        font-size: var(--font-size-md);
        color: var(--primary-color);
        margin-right: 0.5rem;
    }

    .chart-card .card-body {
        padding: var(--card-padding);
    }

    .chart-container {
        position: relative;
        height: 280px;
    }

    /* Row & Column Spacing */
    .row {
        margin-bottom: var(--section-spacing);
    }

    .row:last-child {
        margin-bottom: 0;
    }

    /* Report Type Cards */
    .report-type-cards {
        padding: 10px;
    }

    .report-type-card {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: var(--card-border-radius);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all var(--transition-speed) ease;
        height: 100%;
    }

    .report-type-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .report-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .report-type-content {
        flex: 1;
    }

    .report-type-value {
        font-size: 1.5rem;
        font-weight: var(--font-weight-bold);
        margin-bottom: 0;
        line-height: 1.2;
    }

    .report-type-label {
        color: var(--gray-600);
        font-size: 0.8rem;
        margin-bottom: 0;
    }

    /* Report Type Card Colors */
    .weekly-card {
        background-color: rgba(67, 97, 238, 0.05);
        border-left: 3px solid var(--primary-color);
    }

    .weekly-card .report-type-icon {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .monthly-card {
        background-color: rgba(54, 179, 126, 0.05);
        border-left: 3px solid var(--success-color);
    }

    .monthly-card .report-type-icon {
        background-color: rgba(54, 179, 126, 0.1);
        color: var(--success-color);
    }

    .quarterly-card {
        background-color: rgba(255, 171, 0, 0.05);
        border-left: 3px solid #ffab00;
    }

    .quarterly-card .report-type-icon {
        background-color: rgba(255, 171, 0, 0.1);
        color: #ffab00;
    }

    .semestral-card {
        background-color: rgba(0, 184, 217, 0.05);
        border-left: 3px solid #00b8d9;
    }

    .semestral-card .report-type-icon {
        background-color: rgba(0, 184, 217, 0.1);
        color: #00b8d9;
    }

    .annual-card {
        background-color: rgba(111, 66, 193, 0.05);
        border-left: 3px solid #6f42c1;
    }

    .annual-card .report-type-icon {
        background-color: rgba(111, 66, 193, 0.1);
        color: #6f42c1;
    }

    /* Cluster Cards */
    .cluster-cards {
        padding: 10px;
    }

    .cluster-card {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: var(--card-border-radius);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all var(--transition-speed) ease;
        height: 100%;
        cursor: pointer;
        position: relative;
    }

    .cluster-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .cluster-card.active {
        border: 2px solid;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }



    .cluster-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .cluster-content {
        flex: 1;
    }

    .cluster-value {
        font-size: 1.5rem;
        font-weight: var(--font-weight-bold);
        margin-bottom: 0;
        line-height: 1.2;
    }

    .cluster-label {
        color: var(--gray-600);
        font-size: 0.8rem;
        margin-bottom: 0;
    }

    /* Cluster Card Colors */
    .cluster-1-card {
        background-color: rgba(67, 97, 238, 0.05);
        border-left: 3px solid var(--primary-color);
    }

    .cluster-1-card .cluster-icon {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .cluster-2-card {
        background-color: rgba(54, 179, 126, 0.05);
        border-left: 3px solid var(--success-color);
    }

    .cluster-2-card .cluster-icon {
        background-color: rgba(54, 179, 126, 0.1);
        color: var(--success-color);
    }

    .cluster-3-card {
        background-color: rgba(255, 171, 0, 0.05);
        border-left: 3px solid #ffab00;
    }

    .cluster-3-card .cluster-icon {
        background-color: rgba(255, 171, 0, 0.1);
        color: #ffab00;
    }

    .cluster-4-card {
        background-color: rgba(0, 184, 217, 0.05);
        border-left: 3px solid #00b8d9;
    }

    .cluster-4-card .cluster-icon {
        background-color: rgba(0, 184, 217, 0.1);
        color: #00b8d9;
    }

    /* Responsive Adjustments */
    @media (max-width: 767.98px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .chart-card {
            margin-bottom: 1.5rem;
        }

        .chart-container {
            height: 220px;
        }

        .report-type-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="page-title">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </h2>
    </div>
</div>

<!-- Professional Filter Bar -->
<div class="row mb-4">
    <div class="col-12">
        <div class="filter-bar">
            <form id="dashboardFilterForm" action="{{ route('admin.dashboard') }}" method="GET">
                <div class="row align-items-center">
                    <!-- Search -->
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="filter-label me-2">
                                <i class="fas fa-search text-primary"></i>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="search" name="search"
                                placeholder="Search reports or barangays" value="{{ $search }}">
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-4 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="filter-label me-2">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </div>
                            <div class="d-flex">
                                <input type="date" class="form-control form-control-sm me-1" id="start_date" name="start_date"
                                    placeholder="Start Date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                                <span class="date-separator mx-1">-</span>
                                <input type="date" class="form-control form-control-sm ms-1" id="end_date" name="end_date"
                                    placeholder="End Date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- Report Type -->
                    <div class="col-md-2 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="filter-label me-2">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>
                            <select class="form-select form-select-sm" id="report_type" name="report_type">
                                <option value="">All Types</option>
                                <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ $reportType == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="semestral" {{ $reportType == 'semestral' ? 'selected' : '' }}>Semestral</option>
                                <option value="annual" {{ $reportType == 'annual' ? 'selected' : '' }}>Annual</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cluster -->
                    <div class="col-md-2 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="filter-label me-2">
                                <i class="fas fa-layer-group text-primary"></i>
                            </div>
                            <select class="form-select form-select-sm" id="cluster_id" name="cluster_id">
                                <option value="">All Clusters</option>
                                @foreach($allClusters as $cluster)
                                    <option value="{{ $cluster->id }}" {{ $clusterId == $cluster->id ? 'selected' : '' }}>
                                        Cluster {{ $cluster->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Clear Button -->
                    @if($startDate || $endDate || $reportType || $clusterId || $search)
                    <div class="col-md-1 mb-2 mb-md-0 text-end">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                    @endif
                </div>
                <input type="submit" id="submitFilter" class="d-none">
            </form>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon primary-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $totalReportTypes }}</div>
                    <div class="stat-label">Report Types</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $totalSubmittedReports }}</div>
                    <div class="stat-label">Submitted Reports</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $noSubmissionReports }}</div>
                    <div class="stat-label">No Submissions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon danger-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $lateSubmissions }}</div>
                    <div class="stat-label">Late Submissions</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Submission by Type Chart -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-chart-pie"></i>
                    Submissions by Type
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="submissionTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="col-md-6">
        <div class="chart-card">
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

<div class="row">
    <!-- Report Type Distribution Cards -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-chart-bar"></i>
                    Report Type Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="row report-type-cards">
                    <!-- Weekly Reports Card -->
                    <div class="col-md-6 mb-3">
                        <div class="report-type-card weekly-card">
                            <div class="report-type-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <div class="report-type-content">
                                <h3 class="report-type-value">{{ $weeklyCount }}</h3>
                                <p class="report-type-label">Weekly Reports</p>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Reports Card -->
                    <div class="col-md-6 mb-3">
                        <div class="report-type-card monthly-card">
                            <div class="report-type-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="report-type-content">
                                <h3 class="report-type-value">{{ $monthlyCount }}</h3>
                                <p class="report-type-label">Monthly Reports</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quarterly Reports Card -->
                    <div class="col-md-6 mb-3">
                        <div class="report-type-card quarterly-card">
                            <div class="report-type-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="report-type-content">
                                <h3 class="report-type-value">{{ $quarterlyCount }}</h3>
                                <p class="report-type-label">Quarterly Reports</p>
                            </div>
                        </div>
                    </div>

                    <!-- Semestral Reports Card -->
                    <div class="col-md-6 mb-3">
                        <div class="report-type-card semestral-card">
                            <div class="report-type-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="report-type-content">
                                <h3 class="report-type-value">{{ $semestralCount }}</h3>
                                <p class="report-type-label">Semestral Reports</p>
                            </div>
                        </div>
                    </div>

                    <!-- Annual Reports Card -->
                    <div class="col-md-6 mb-3">
                        <div class="report-type-card annual-card">
                            <div class="report-type-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="report-type-content">
                                <h3 class="report-type-value">{{ $annualCount }}</h3>
                                <p class="report-type-label">Annual Reports</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions per Cluster Cards -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-layer-group"></i>
                    Submissions per Cluster
                    <button id="clear-filter" class="btn btn-sm btn-outline-secondary float-end d-none">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </h5>
            </div>
            <div class="card-body">
                <div class="row cluster-cards">
                    @foreach($clusterSubmissions as $clusterName => $submissionCount)
                    <div class="col-md-6 mb-3">
                        <div class="cluster-card cluster-{{ substr($clusterName, -1) }}-card"
                             data-cluster-id="{{ substr($clusterName, -1) }}"
                             onclick="filterByCluster({{ substr($clusterName, -1) }})">
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
</div>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers with validation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const reportTypeSelect = document.getElementById('report_type');
    const clusterSelect = document.getElementById('cluster_id');
    const searchInput = document.getElementById('search');
    const filterForm = document.getElementById('dashboardFilterForm');

    // Check if cluster filter is active on page load
    const currentClusterId = clusterSelect.value;
    if (currentClusterId) {
        document.getElementById('clear-filter').classList.remove('d-none');
        const activeCard = document.querySelector(`.cluster-card[data-cluster-id="${currentClusterId}"]`);
        if (activeCard) {
            activeCard.classList.add('active');
        }
    }

    // Function to submit the form
    function submitFilterForm() {
        // Show loading indicator
        document.body.style.cursor = 'wait';

        // Submit the form
        filterForm.submit();
    }

    // Validate date range and submit form on change
    startDateInput.addEventListener('change', function() {
        if (endDateInput.value && this.value > endDateInput.value) {
            alert('Start date cannot be after end date');
            this.value = '';
            return;
        }
        submitFilterForm();
    });

    endDateInput.addEventListener('change', function() {
        if (startDateInput.value && this.value < startDateInput.value) {
            alert('End date cannot be before start date');
            this.value = '';
            return;
        }
        submitFilterForm();
    });

    // Submit form when select options change
    reportTypeSelect.addEventListener('change', submitFilterForm);
    clusterSelect.addEventListener('change', submitFilterForm);

    // Add debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }

    // Debounced search function (wait 500ms after typing stops)
    const debouncedSearch = debounce(function() {
        submitFilterForm();
    }, 500);

    // Add event listener for search input
    searchInput.addEventListener('input', debouncedSearch);

    // Add event listener for search input to submit form on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitFilterForm();
        }
    });
    // Common chart options
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6c757d';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(45, 55, 72, 0.9)';
    Chart.defaults.plugins.tooltip.padding = 8;
    Chart.defaults.plugins.tooltip.cornerRadius = 4;
    Chart.defaults.plugins.tooltip.titleFont = { weight: 600, size: 13 };
    Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };
    Chart.defaults.plugins.tooltip.displayColors = true;
    Chart.defaults.plugins.tooltip.boxPadding = 4;

    // Modern minimal color palette
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

    // Chart colors
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

    // Submission Type Chart
    const submissionTypeCtx = document.getElementById('submissionTypeChart').getContext('2d');
    new Chart(submissionTypeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Weekly', 'Monthly', 'Quarterly', 'Semestral', 'Annual'],
            datasets: [{
                data: [
                    {{ $weeklyCount }},
                    {{ $monthlyCount }},
                    {{ $quarterlyCount }},
                    {{ $semestralCount }},
                    {{ $annualCount }}
                ],
                backgroundColor: chartColors.slice(0, 5),
                borderWidth: 0,
                borderRadius: 4,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 10,
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Distribution of Submissions by Report Type',
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
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    // Monthly Trend Chart
    const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(monthlyTrendCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Submissions',
                data: {{ json_encode($submissionsByMonth) }},
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

    // No chart needed for Report Type Distribution as it's now displayed as cards

    // No chart needed for Cluster Submissions as it's now displayed as cards



    // Function to filter by cluster
    function filterByCluster(clusterId) {
        // Show the clear filter button
        document.getElementById('clear-filter').classList.remove('d-none');

        // Set active class on the selected cluster card
        document.querySelectorAll('.cluster-card').forEach(card => {
            card.classList.remove('active');
        });
        document.querySelector(`.cluster-card[data-cluster-id="${clusterId}"]`).classList.add('active');

        // Update the filter in the URL without reloading the page
        const url = new URL(window.location);
        url.searchParams.set('cluster_id', clusterId);
        window.history.pushState({}, '', url);

        // Submit the form to apply the filter
        document.getElementById('cluster_id').value = clusterId;
        submitFilterForm();
    }

    // Function to clear the filter
    document.getElementById('clear-filter').addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent the click from bubbling up

        // Hide the clear filter button
        document.getElementById('clear-filter').classList.add('d-none');

        // Remove active class from all cluster cards
        document.querySelectorAll('.cluster-card').forEach(card => {
            card.classList.remove('active');
        });

        // Clear the filter in the URL
        const url = new URL(window.location);
        url.searchParams.delete('cluster_id');
        window.history.pushState({}, '', url);

        // Reset the filter and submit the form
        document.getElementById('cluster_id').value = '';
        submitFilterForm();
    });
});
</script>
@endpush
@endsection
