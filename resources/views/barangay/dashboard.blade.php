<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #343a40;
            padding: 20px;
            color: white;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,.75);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,.1);
        }
        .stat-card {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .stat-card .label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .recent-reports {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .report-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .report-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="sidebar">
        <h4 class="mb-4">Barangay Dashboard</h4>
        <nav class="nav flex-column">
            <a class="nav-link active" href="{{ route('dashboard') }}">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a class="nav-link" href="{{ route('reports.submit') }}">
                <i class="bi bi-plus-circle"></i> Submit Report
            </a>
            <a class="nav-link" href="{{ route('reports.view') }}">
                <i class="bi bi-list-ul"></i> View Reports
            </a>
            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Dashboard Overview</h2>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card text-primary">
                        <div class="icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="number">{{ $totalReports }}</div>
                        <div class="label">Total Reports</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-success">
                        <div class="icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="number">{{ $approvedReports }}</div>
                        <div class="label">Approved Reports</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-warning">
                        <div class="icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="number">{{ $pendingReports }}</div>
                        <div class="label">Pending Reports</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-danger">
                        <div class="icon">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                        <div class="number">{{ $rejectedReports }}</div>
                        <div class="label">Rejected Reports</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="recent-reports">
                        <h4 class="mb-4">Recent Reports</h4>
                        @forelse($recentReports as $report)
                            <div class="report-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $report->reportType->name }}</h5>
                                        <small class="text-muted">
                                            Submitted on: {{ $report->created_at->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                @if($report->remarks)
                                    <div class="mt-2">
                                        <small class="text-muted">Remarks: {{ $report->remarks }}</small>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted">No reports submitted yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="recent-reports">
                        <h4 class="mb-4">Upcoming Deadlines</h4>
                        @forelse($upcomingDeadlines as $deadline)
                            <div class="report-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $deadline->name }}</h5>
                                        <small class="text-muted">
                                            Due: {{ \Carbon\Carbon::parse($deadline->deadline)->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-info">
                                        {{ ucfirst($deadline->frequency) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted">No upcoming deadlines.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
