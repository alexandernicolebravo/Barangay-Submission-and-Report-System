@extends('admin.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
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
    <!-- Report Type Distribution -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-chart-bar"></i>
                    Report Type Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="reportTypeDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions per Cluster -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-layer-group"></i>
                    Submissions per Cluster
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="clusterSubmissionsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Report Type Distribution Chart
    const reportTypeDistributionCtx = document.getElementById('reportTypeDistributionChart').getContext('2d');
    new Chart(reportTypeDistributionCtx, {
        type: 'bar',
        data: {
            labels: ['Weekly', 'Monthly', 'Quarterly', 'Semestral', 'Annual'],
            datasets: [{
                label: 'Submissions',
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
                borderSkipped: false,
                barPercentage: 0.6,
                categoryPercentage: 0.7
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
                    text: 'Report Type Distribution',
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
                        text: 'Report Type',
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

    // Cluster Submissions Chart
    const clusterSubmissionsCtx = document.getElementById('clusterSubmissionsChart').getContext('2d');
    new Chart(clusterSubmissionsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($clusterSubmissions)) !!},
            datasets: [{
                label: 'Submissions',
                data: {!! json_encode(array_values($clusterSubmissions)) !!},
                backgroundColor: chartColors,
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.6,
                categoryPercentage: 0.7
            }]
        },
        options: {
            indexAxis: 'y', // This makes the chart vertical
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Number of Submissions per Cluster',
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
                    title: {
                        display: true,
                        text: 'Cluster',
                        font: {
                            size: 12
                        },
                        padding: {
                            right: 10
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
});
</script>
@endpush
@endsection
