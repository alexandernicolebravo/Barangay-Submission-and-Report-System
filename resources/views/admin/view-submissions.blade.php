<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            padding: 20px 0;
            color: white;
        }

        .sidebar .nav-link {
            color: white;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: var(--secondary-color);
        }

        .sidebar .nav-link.active {
            background-color: var(--accent-color);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <h4>Admin Panel</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('admin.create-report') }}">
                        <i class="fas fa-file-alt me-2"></i> Create Report Type
                    </a>
                    <a class="nav-link active" href="{{ route('view.submissions') }}">
                        <i class="fas fa-list me-2"></i> View Submissions
                    </a>
                    <a class="nav-link" href="{{ route('admin.create-report') }}">
                        <i class="fas fa-chart-bar me-2"></i> Report Types
                    </a>
                    <a class="nav-link" href="{{ route('barangay.submissions') }}">
                        <i class="fas fa-building me-2"></i> Barangay Submissions
                    </a>
                    <a class="nav-link" href="{{ route('barangay.overdue-reports') }}">
                        <i class="fas fa-exclamation-triangle me-2"></i> Overdue Reports
                    </a>
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2>View Submissions</h2>
                        <hr>
                    </div>
                </div>

                @foreach(['weekly', 'monthly', 'quarterly', 'semestral', 'annual'] as $type)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ ucfirst($type) }} Reports</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Submitted By</th>
                                            <th>Report Type</th>
                                            <th>Status</th>
                                            <th>Submission Time</th>
                                            <th>File</th>
                                            <th>Submitted At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reports[$type] as $report)
                                            <tr>
                                                <td>{{ $report->id }}</td>
                                                <td>{{ $report->user->name }}</td>
                                                <td>{{ $report->reportType->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($report->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $submittedTimestamp = strtotime($report->created_at);
                                                        $deadlineTimestamp = strtotime($report->deadline);
                                                        $deadlineTimestamp = $deadlineTimestamp + (24 * 60 * 60);
                                                        $isLate = $submittedTimestamp > $deadlineTimestamp;
                                                        $submissionStatus = $isLate ? 'Late' : 'On Time';
                                                        $badgeClass = $isLate ? 'danger' : 'success';
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeClass }}">
                                                        {{ $submissionStatus }}
                                                    </span>
                                                    @if($isLate)
                                                        <small class="text-muted d-block">
                                                            Deadline: {{ date('Y-m-d', $deadlineTimestamp - (24 * 60 * 60)) }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($report->file_path)
                                                        <a href="{{ Storage::url($report->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View File
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No file attached</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('Y-m-d H:i:s', $submittedTimestamp) }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal{{ $report->id }}">
                                                        <i class="fas fa-edit"></i> Update Status
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Update Status Modal -->
                                            <div class="modal fade" id="updateModal{{ $report->id }}" tabindex="-1" aria-labelledby="updateModalLabel{{ $report->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="updateModalLabel{{ $report->id }}">Update Report Status</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('update.report', $report->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="status" class="form-label">Status</label>
                                                                    <select name="status" id="status" class="form-select" required>
                                                                        <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                        <option value="approved" {{ $report->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                                        <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="remarks" class="form-label">Remarks</label>
                                                                    <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ $report->remarks }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No {{ $type }} reports found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
