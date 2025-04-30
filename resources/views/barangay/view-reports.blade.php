<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
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
        .report-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .report-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.9em;
            padding: 5px 10px;
            border-radius: 15px;
        }
        .filter-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="sidebar">
        <h4 class="mb-4">Report Management</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a class="nav-link" href="{{ route('reports.submit') }}">
                <i class="bi bi-plus-circle"></i> Submit Report
            </a>
            <a class="nav-link active" href="{{ route('reports.view') }}">
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
            <h2 class="mb-4">Submitted Reports</h2>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="filter-section">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Report Type</label>
                        <select class="form-select" id="reportTypeFilter">
                            <option value="">All Types</option>
                            @foreach($reportTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Range</label>
                        <input type="date" class="form-control" id="dateFilter">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary w-100" id="applyFilter">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <div class="row" id="reportsContainer">
                @forelse($reports as $report)
                    <div class="col-md-6">
                        <div class="report-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">{{ $report->reportType->name }}</h5>
                                    <p class="text-muted mb-1">
                                        <small>Submitted on: {{ $report->created_at->format('M d, Y H:i') }}</small>
                                    </p>
                                    <p class="text-muted mb-1">
                                        <small>Deadline: {{ $report->deadline }}</small>
                                    </p>
                                </div>
                                <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </div>

                            @if($report->remarks)
                                <div class="mt-2">
                                    <p class="mb-1"><strong>Remarks:</strong></p>
                                    <p class="text-muted">{{ $report->remarks }}</p>
                                </div>
                            @endif

                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $report->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-eye"></i> View File
                                </a>
                                @if($report->status !== 'approved')
                                    <a href="{{ route('reports.resubmit', $report->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-arrow-clockwise"></i> Resubmit
                                    </a>
                                @endif
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteReport({{ $report->id }})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            No reports submitted yet.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.getElementById('applyFilter').addEventListener('click', function() {
            const reportType = document.getElementById('reportTypeFilter').value;
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;

            const reports = document.querySelectorAll('.report-card');
            reports.forEach(report => {
                const reportTypeMatch = !reportType || report.dataset.reportType === reportType;
                const statusMatch = !status || report.dataset.status === status;
                const dateMatch = !date || report.dataset.date === date;

                if (reportTypeMatch && statusMatch && dateMatch) {
                    report.closest('.col-md-6').style.display = '';
                } else {
                    report.closest('.col-md-6').style.display = 'none';
                }
            });
        });

        // Delete report functionality
        function deleteReport(id) {
            if (confirm('Are you sure you want to delete this report?')) {
                fetch(`/reports/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete report. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the report.');
                });
            }
        }
    </script>
</body>
</html>
