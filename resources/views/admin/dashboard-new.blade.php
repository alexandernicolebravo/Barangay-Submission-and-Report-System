@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-filter"></i>
                        Filter Dashboard
                    </h5>
                </div>
                <div class="card-body">
                    <form id="dashboardFilterForm" action="{{ route('admin.dashboard') }}" method="GET">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="report_type">Report Type</label>
                                    <select class="form-control" id="report_type" name="report_type">
                                        <option value="">All Types</option>
                                        <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ request('report_type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="semestral" {{ request('report_type') == 'semestral' ? 'selected' : '' }}>Semestral</option>
                                        <option value="annual" {{ request('report_type') == 'annual' ? 'selected' : '' }}>Annual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="cluster_id">Cluster</label>
                                    <select class="form-control" id="cluster_id" name="cluster_id">
                                        <option value="">All Clusters</option>
                                        @foreach(App\Models\Cluster::all() as $cluster)
                                            <option value="{{ $cluster->id }}" {{ request('cluster_id') == $cluster->id ? 'selected' : '' }}>
                                                Cluster {{ $cluster->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" placeholder="Search barangay..." value="{{ request('search') }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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

    <!-- Charts Row -->
    <div class="row">
        <!-- Submission Type Chart -->
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
                        <button id="clear-filter" class="btn btn-sm btn-outline-secondary float-end {{ request('cluster_id') ? '' : 'd-none' }}">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row cluster-cards">
                        @foreach($clusterSubmissions as $clusterName => $submissionCount)
                        <div class="col-md-6 mb-3">
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
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Chart container styles */
    .chart-container {
        position: relative;
        height: 300px;
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
    }

    .report-type-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
    // Initialize date pickers with validation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const reportTypeSelect = document.getElementById('report_type');
    const clusterSelect = document.getElementById('cluster_id');
    const searchInput = document.getElementById('search');
    const filterForm = document.getElementById('dashboardFilterForm');
    const clearFilterBtn = document.getElementById('clear-filter');

    // Function to submit the form
    function submitFilterForm() {
        document.body.style.cursor = 'wait';
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

    // Clear filter button
    clearFilterBtn.addEventListener('click', function() {
        clusterSelect.value = '';
        submitFilterForm();
    });

    // Add click event to cluster cards
    document.querySelectorAll('.cluster-card').forEach(card => {
        card.addEventListener('click', function() {
            const clusterId = this.getAttribute('data-cluster-id');
            clusterSelect.value = clusterId;
            submitFilterForm();
        });
    });

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

    // Initialize charts
    function initCharts() {
        // Submission Type Chart
        const submissionTypeCtx = document.getElementById('submissionTypeChart');
        if (submissionTypeCtx) {
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
        }

        // Monthly Trend Chart
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
        if (monthlyTrendCtx) {
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
        }
    }

    // Initialize charts
    initCharts();
});
</script>
@endpush
